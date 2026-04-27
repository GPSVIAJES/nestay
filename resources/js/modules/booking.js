import { NestayAPI } from './api';

export const BookingModule = {

    /**
     * STEP 3 — Prebook validation.
     * Runs on page load in booking.blade.php.
     *
     * Input:  book_hash (h-... from hotelpage)
     * Output: Stores the new p-... hash for use in the booking steps.
     *
     * If price changed, user must be notified before proceeding.
     */
    _prebookHash: null,  // Stores the p-... hash returned by prebook

    async prebookValidate(bookHash) {
        if (!bookHash) return;

        try {
            const data = await NestayAPI.prebook(bookHash, 0);

            if (data?.status !== 'ok') {
                this.showBanner('⚠️ No se pudo verificar la disponibilidad. Por favor, regresa y elige otra habitación.', 'error');
                return;
            }

            // Store the p-... hash for the booking steps
            this._prebookHash = data?.data?.book_hash;

            if (data?.data?.price_changed) {
                const newPrice = data?.data?.rate?.payment_options?.payment_types?.[0]?.show_amount
                              || data?.data?.rate?.total_price
                              || '—';
                this.showBanner(
                    `⚠️ <strong>El precio ha cambiado.</strong> El precio actualizado es $${newPrice} USD. ¿Deseas continuar con este nuevo precio?`,
                    'warning'
                );
            }

        } catch (e) {
            console.warn('Prebook check failed:', e.message);
        }
    },

    /**
     * Full booking flow — called on form submit.
     *
     * Step 4.1 POST /api/booking-form  → get partner_order_id
     * Step 4.2 POST /api/book          → initiate booking at ETG
     * Step 4.3 GET  /api/booking-status/{partner_order_id} → poll until done
     */
    async submitBooking(e, bookingParams) {
        e.preventDefault();

        const btn = document.getElementById('submit-booking-btn');
        if (!btn) return;

        const spinner = '<div class="spinner" style="width:20px;height:20px;border-width:2px;border-color:rgba(255,255,255,0.3);border-top-color:white;display:inline-block;"></div>';
        btn.disabled = true;
        btn.innerHTML = `${spinner} Verificando disponibilidad...`;

        // Use the p-... hash from prebook, fallback to original h-... hash
        const effectiveHash = this._prebookHash || bookingParams.get('book_hash');

        try {
            // ── STEP 4.1: Create booking form (link our order to ETG) ────────
            const formResult = await NestayAPI.createBookingForm(effectiveHash);

            if (formResult.status !== 'ok' || !formResult.partner_order_id) {
                const error = formResult.error || 'unknown';
                if (error === 'max_retries_exceeded') {
                    throw new Error('No se pudo crear la reserva. Por favor, busca de nuevo.');
                }
                throw new Error(formResult.message || 'No se pudo inicializar la reserva.');
            }

            const partnerOrderId = formResult.partner_order_id;
            btn.innerHTML = `${spinner} Confirmando con el hotel...`;

            // ── STEP 4.2: Start the booking (async — ETG queues with supplier) ─
            const bookPayload = {
                partner_order_id:    partnerOrderId,
                book_hash:           effectiveHash,
                hotel_id:            bookingParams.get('hotel_id'),
                hotel_name:          bookingParams.get('hotel_name'),
                hotel_city:          bookingParams.get('hotel_city')    || '',
                hotel_country:       bookingParams.get('hotel_country') || '',
                hotel_image:         bookingParams.get('hotel_image')   || '',
                check_in:            bookingParams.get('check_in'),
                check_out:           bookingParams.get('check_out'),
                guests:              parseInt(bookingParams.get('guests'))      || 1,
                total_price:         parseFloat(bookingParams.get('total_price')) || 0,
                currency:            bookingParams.get('currency') || 'USD',
                cancellation_policy: bookingParams.get('cancellation_policy')  || '',
                guest: {
                    first_name: document.getElementById('first_name').value.trim(),
                    last_name:  document.getElementById('last_name').value.trim(),
                    email:      document.getElementById('email').value.trim(),
                    phone:      document.getElementById('phone')?.value.trim() || '',
                },
            };

            const startResult = await NestayAPI.book(bookPayload);

            if (startResult.status !== 'ok') {
                const error = startResult.error || '';
                if (['booking_form_expired', 'rate_not_found'].includes(error)) {
                    throw new Error('La tarifa ya no está disponible. Por favor, busca de nuevo.');
                }
                throw new Error(startResult.message || 'No se pudo iniciar la reserva.');
            }

            // ── STEP 4.3: Poll until confirmed or failed ─────────────────────
            this.showBanner(
                '🔄 Tu reserva está siendo procesada. Esto puede tomar unos segundos...',
                'info'
            );

            await this.pollUntilConfirmed(partnerOrderId);

        } catch (err) {
            this.showBanner(err.message || 'Error de conexión. Revisa tu internet e intenta de nuevo.', 'error');
            btn.disabled = false;
            btn.textContent = 'Confirmar y reservar';
        }
    },

    /**
     * Poll /api/booking-status/{partnerOrderId} every 3 seconds.
     * Redirects to confirm page when status = 'confirmed'.
     * ETG recommends polling for up to 60 seconds.
     *
     * Terminal failures: soldout, book_limit, provider, failed
     * Keep polling:      pending (processing/timeout/unknown)
     */
    async pollUntilConfirmed(partnerOrderId, maxAttempts = 20, intervalMs = 3000) {
        for (let attempt = 1; attempt <= maxAttempts; attempt++) {
            await new Promise(resolve => setTimeout(resolve, intervalMs));

            try {
                const result = await NestayAPI.getBookingStatus(partnerOrderId);

                if (result.booking_status === 'confirmed') {
                    // Redirect to confirmation page using partner_order_id
                    window.location.href = `/booking/${partnerOrderId}/confirm`;
                    return;
                }

                if (result.booking_status === 'failed') {
                    throw new Error('La reserva no pudo ser confirmada por el hotel. Por favor, elige otra habitación.');
                }

                if (result.booking_status === 'cancelled') {
                    throw new Error('La reserva fue cancelada. Por favor, intenta de nuevo.');
                }

                // Still 'pending' — continue polling

            } catch (err) {
                // Re-throw final errors
                if (err.message && (
                    err.message.includes('no pudo ser confirmada') ||
                    err.message.includes('cancelada')
                )) {
                    throw err;
                }
                console.warn(`[Booking] Poll attempt ${attempt} failed:`, err.message);
            }
        }

        throw new Error('La confirmación está tardando más de lo esperado. Revisa "Mis reservas" en unos minutos o contacta soporte.');
    },

    // ── UI Helpers ────────────────────────────────────────────────────────────

    showBanner(html, type = 'info') {
        const banner = document.getElementById('prebook-status');
        if (!banner) return;

        const styles = {
            info:    { bg: '#e0f2fe', border: '#38bdf8', color: '#0c4a6e' },
            warning: { bg: '#fef3c7', border: '#fbbf24', color: '#92400e' },
            error:   { bg: '#fee2e2', border: '#fca5a5', color: '#991b1b' },
            success: { bg: '#dcfce7', border: '#86efac', color: '#14532d' },
        };

        const s = styles[type] || styles.info;
        banner.style.display         = 'block';
        banner.style.backgroundColor = s.bg;
        banner.style.border          = `1px solid ${s.border}`;
        banner.style.color           = s.color;
        banner.style.padding         = '1rem 1.25rem';
        banner.style.borderRadius    = '0.75rem';
        banner.style.marginBottom    = '1.5rem';
        banner.style.fontSize        = '14px';
        banner.innerHTML             = html;
    },
};
