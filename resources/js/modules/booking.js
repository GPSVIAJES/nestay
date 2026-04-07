import { NestayAPI } from './api';

export const BookingModule = {

    /**
     * Validate price before the user submits.
     * Runs automatically on page load from booking.blade.php.
     */
    async prebookValidate(bookHash) {
        if (!bookHash) return;

        try {
            const data = await NestayAPI.prebook(bookHash);

            if (data?.data?.price_changed) {
                this.showBanner(
                    `⚠️ <strong>El precio ha cambiado.</strong> El precio actualizado es $${data?.data?.rate?.total_price || '—'}`,
                    'warning'
                );
            }
        } catch (e) {
            console.warn('Prebook check failed (mock mode OK):', e.message);
        }
    },

    /**
     * Submit booking form.
     * Step 1: POST /api/book → get order_id (status = pending)
     * Step 2: Poll GET /api/booking-status/{id} until confirmed or failed
     */
    async submitBooking(e, bookingParams) {
        e.preventDefault();

        const btn = document.getElementById('submit-booking-btn');
        if (!btn) return;

        btn.disabled = true;
        btn.innerHTML = '<div class="spinner" style="width:20px;height:20px;border-width:2px;border-color:rgba(255,255,255,0.3);border-top-color:white;display:inline-block;"></div> Procesando...';

        const payload = {
            book_hash:           bookingParams.get('book_hash'),
            hotel_id:            bookingParams.get('hotel_id'),
            hotel_name:          bookingParams.get('hotel_name'),
            hotel_city:          bookingParams.get('hotel_city')    || '',
            hotel_country:       bookingParams.get('hotel_country') || '',
            hotel_image:         bookingParams.get('hotel_image')   || '',
            check_in:            bookingParams.get('check_in'),
            check_out:           bookingParams.get('check_out'),
            guests:              parseInt(bookingParams.get('guests'))      || 1,
            total_price:         parseFloat(bookingParams.get('total_price')) || 0,
            cancellation_policy: bookingParams.get('cancellation_policy')  || '',
            guest: {
                first_name: document.getElementById('first_name').value.trim(),
                last_name:  document.getElementById('last_name').value.trim(),
                email:      document.getElementById('email').value.trim(),
                phone:      document.getElementById('phone')?.value.trim() || '',
            },
        };

        try {
            // ── STEP 1: Start the booking (ETG async) ──────────────────────
            const startResult = await NestayAPI.book(payload);

            if (startResult.status !== 'ok' || !startResult.order_id) {
                throw new Error(startResult.message || 'No se pudo iniciar la reserva.');
            }

            const orderId = startResult.order_id;

            // ── STEP 2: Show processing state & start polling ─────────────
            btn.innerHTML = '<div class="spinner" style="width:20px;height:20px;border-width:2px;border-color:rgba(255,255,255,0.3);border-top-color:white;display:inline-block;"></div> Confirmando con el hotel...';

            this.showBanner(
                '🔄 Tu reserva está siendo procesada. Esto puede tomar unos segundos...',
                'info'
            );

            await this.pollUntilConfirmed(orderId);

        } catch (err) {
            this.showBanner(err.message || 'Error de conexión. Revisa tu internet e intenta de nuevo.', 'error');
            btn.disabled = false;
            btn.textContent = 'Confirmar y reservar nido';
        }
    },

    /**
     * Poll /api/booking-status/{id} every 3 seconds.
     * Redirects to confirm page when status = 'confirmed'.
     * Throws after 60 seconds (20 attempts × 3s).
     */
    async pollUntilConfirmed(orderId, maxAttempts = 20, intervalMs = 3000) {
        for (let attempt = 1; attempt <= maxAttempts; attempt++) {
            await new Promise(resolve => setTimeout(resolve, intervalMs));

            try {
                const result = await NestayAPI.getBookingStatus(orderId);

                if (result.booking_status === 'confirmed') {
                    // Success — redirect to confirmation page
                    window.location.href = `/booking/${orderId}/confirm`;
                    return;
                }

                if (result.booking_status === 'failed' || result.booking_status === 'cancelled') {
                    throw new Error('La reserva no pudo ser confirmada por el hotel. Por favor, elige otra habitación.');
                }

                // Still 'pending' — continue polling
            } catch (err) {
                // Re-throw non-recoverable errors (failed status)
                if (err.message && err.message.includes('no pudo ser confirmada')) {
                    throw err;
                }
                console.warn(`[Booking] Poll attempt ${attempt} failed:`, err.message);
            }
        }

        // Timeout after maxAttempts
        throw new Error('La confirmación está tardando más de lo esperado. Revisa "Mis reservas" en unos minutos o contacta soporte.');
    },

    // ── UI Helpers ───────────────────────────────────────────────────────────

    /** Display a status banner above the form. */
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
        banner.style.display       = 'block';
        banner.style.backgroundColor = s.bg;
        banner.style.border        = `1px solid ${s.border}`;
        banner.style.color         = s.color;
        banner.style.padding       = '1rem 1.25rem';
        banner.style.borderRadius  = '0.75rem';
        banner.style.marginBottom  = '1.5rem';
        banner.style.fontSize      = '14px';
        banner.innerHTML           = html;
    },
};
