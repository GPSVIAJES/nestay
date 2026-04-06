import { NestayAPI } from './api';

export const BookingModule = {
    async prebookValidate(bookHash) {
        if (!bookHash) return;
        
        try {
            const data = await NestayAPI.prebook(bookHash);
            
            if (data?.data?.price_changed) {
                const banner = document.getElementById('prebook-status');
                if (banner) {
                    banner.style.display = 'block';
                    banner.style.backgroundColor = '#fef3c7';
                    banner.style.border = '1px solid #fbbf24';
                    banner.style.color = '#92400e';
                    banner.innerHTML = `⚠️ <strong>El precio ha cambiado.</strong> El precio actualizado es $${data?.data?.rate?.total_price || '—'}`;
                }
            }
        } catch (e) {
            console.warn('Prebook check failed (mock mode OK):', e.message);
        }
    },

    async submitBooking(e, bookingParams) {
        e.preventDefault();
        
        const btn = document.getElementById('submit-booking-btn');
        if (!btn) return;

        btn.disabled = true;
        btn.innerHTML = '<div class="spinner" style="width:20px;height:20px;border-width:2px;border-color:rgba(255,255,255,0.3);border-top-color:white"></div> Procesando...';

        const payload = {
            book_hash: bookingParams.get('book_hash'),
            hotel_id: bookingParams.get('hotel_id'),
            hotel_name: bookingParams.get('hotel_name'),
            hotel_city: bookingParams.get('hotel_city') || '',
            hotel_country: bookingParams.get('hotel_country') || '',
            hotel_image: bookingParams.get('hotel_image') || '',
            check_in: bookingParams.get('check_in'),
            check_out: bookingParams.get('check_out'),
            guests: parseInt(bookingParams.get('guests')) || 1,
            total_price: parseFloat(bookingParams.get('total_price')) || 0,
            cancellation_policy: bookingParams.get('cancellation_policy') || '',
            guest: {
                first_name: document.getElementById('first_name').value,
                last_name: document.getElementById('last_name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
            }
        };

        try {
            const data = await NestayAPI.book(payload);
            
            if (data.status === 'ok') {
                window.location.href = `/booking/${data.data.order_id}/confirm`;
            } else {
                this.showError('No se pudo completar la reserva. Por favor, inténtalo de nuevo.');
                btn.disabled = false;
                btn.textContent = 'Confirmar reserva';
            }
        } catch (e) {
            this.showError(e.message || 'Error de conexión. Revisa tu internet e intenta de nuevo.');
            btn.disabled = false;
            btn.textContent = 'Confirmar reserva';
        }
    },

    showError(msg) {
        const banner = document.getElementById('prebook-status');
        if (banner) {
            banner.style.display = 'block';
            banner.style.backgroundColor = '#fee2e2';
            banner.style.border = '1px solid #fca5a5';
            banner.style.color = '#991b1b';
            banner.style.padding = '1rem';
            banner.style.borderRadius = '0.75rem';
            banner.style.marginBottom = '1.5rem';
            banner.textContent = `❌ ${msg}`;
        }
    }
};
