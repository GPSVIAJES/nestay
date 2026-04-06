import { NestayAPI } from './api';

export const HotelModule = {
    currentSlide: 0,
    hotelData: null,

    async loadHotel(hotelId, urlParams) {
        try {
            const data = await NestayAPI.getHotelDetails({
                hotel_id: hotelId,
                checkin: urlParams.get('checkin'),
                checkout: urlParams.get('checkout'),
                adults: parseInt(urlParams.get('adults')) || 2,
            });

            this.hotelData = data?.data;
            if (!this.hotelData) throw new Error('No data');
            this.renderHotel(this.hotelData, urlParams);
        } catch (e) {
            this.renderError();
        }
    },

    renderHotel(data, urlParams) {
        const hotel = data.hotel;
        const rates = data.rates;
        
        const loadingEl = document.getElementById('hotel-loading');
        const contentEl = document.getElementById('hotel-content');

        if (loadingEl) loadingEl.style.display = 'none';
        if (contentEl) contentEl.style.display = 'block';

        document.title = `${hotel.name} — Nestay`;

        // Galllery
        this.renderGallery(hotel.images);

        // Meta
        const starsEl = document.getElementById('hotel-stars');
        const ratingBadgeEl = document.getElementById('hotel-rating-badge');
        const nameEl = document.getElementById('hotel-name');
        const addressEl = document.getElementById('hotel-address');
        const descriptionEl = document.getElementById('hotel-description');

        if (starsEl) starsEl.textContent = '★'.repeat(hotel.stars || 0);
        if (ratingBadgeEl) ratingBadgeEl.textContent = `${hotel.rating || '-'} / 10 · ${(hotel.reviews || 0).toLocaleString()} reseñas`;
        if (nameEl) nameEl.textContent = hotel.name;
        if (addressEl) addressEl.querySelector('span').textContent = hotel.address;
        if (descriptionEl) descriptionEl.textContent = hotel.description || '';

        // Amenities
        this.renderAmenities(hotel.amenities);

        // Policies
        this.renderPolicies(hotel.policies);

        // Rates
        this.renderRates(rates, hotel, urlParams);
    },

    renderGallery(images = []) {
        const slides = document.getElementById('gallery-slides');
        const dots = document.getElementById('gallery-dots');

        if (slides) {
            slides.innerHTML = images.map(img => `
                <div style="flex-shrink:0;width:100%;height:100%">
                    <img src="${img}" style="width:100%;height:100%;object-fit:cover" alt="Hotel image">
                </div>`).join('');
        }

        if (dots) {
            dots.innerHTML = images.map((_, i) => `
                <div class="gallery-dot" data-index="${i}" style="width:8px;height:8px;border-radius:50%;background:${i === 0 ? 'white' : 'rgba(255,255,255,0.5)'};cursor:pointer;transition:background 0.2s"></div>
            `).join('');

            dots.querySelectorAll('.gallery-dot').forEach(dot => {
                dot.addEventListener('click', (e) => this.goToSlide(parseInt(e.target.dataset.index)));
            });
        }
    },

    goToSlide(n) {
        this.currentSlide = n;
        const slides = document.getElementById('gallery-slides');
        if (slides) slides.style.transform = `translateX(-${n * 100}%)`;
        
        document.querySelectorAll('.gallery-dot').forEach((d, i) => {
            d.style.background = i === n ? 'white' : 'rgba(255,255,255,0.5)';
        });
    },

    prevSlide() {
        const total = document.querySelectorAll('#gallery-slides > div').length;
        this.goToSlide((this.currentSlide - 1 + total) % total);
    },

    nextSlide() {
        const total = document.querySelectorAll('#gallery-slides > div').length;
        this.goToSlide((this.currentSlide + 1) % total);
    },

    renderAmenities(amenities = {}) {
        const div = document.getElementById('hotel-amenities');
        const amenMap = {
            wifi: '📶 WiFi', pool: '🏊 Piscina', spa: '💆 Spa', 
            restaurant: '🍽️ Restaurante', bar: '🍸 Bar', gym: '💪 Gimnasio', 
            parking: '🅿️ Parking', concierge: '🛎️ Concierge', roomservice: '🛏️ Room Service', 
            jacuzzi: '🛁 Jacuzzi', terrace: '🌿 Terraza'
        };

        if (div) {
            div.innerHTML = Object.keys(amenities).map(k =>
                `<span class="amenity-tag" style="padding:4px 12px;font-size:0.85rem">${amenMap[k] || k}</span>`
            ).join('');
        }
    },

    renderPolicies(policies = {}) {
        const div = document.getElementById('hotel-policies');
        if (div) {
            div.innerHTML = Object.entries(policies).map(([k, v]) => `
                <div style="display:flex;gap:var(--space-3);padding:var(--space-2) 0;border-bottom:1px solid var(--gray-100)">
                    <span style="font-weight:600;color:var(--gray-700);min-width:100px;font-size:0.875rem;text-transform:capitalize">${k.replace('_', ' ')}</span>
                    <span style="color:var(--gray-500);font-size:0.875rem">${v}</span>
                </div>`).join('');
        }
    },

    renderRates(rates = [], hotel, urlParams) {
        const div = document.getElementById('rate-cards');
        const checkin = urlParams.get('checkin');
        const checkout = urlParams.get('checkout');
        const nights = checkin && checkout ? Math.ceil((new Date(checkout) - new Date(checkin)) / 86400000) : 1;

        if (div) {
            div.innerHTML = rates.map((rate, i) => `
                <div style="border:2px solid ${i === 0 ? 'var(--primary-500)' : 'var(--gray-200)'};border-radius:var(--radius-lg);padding:var(--space-4);${i === 0 ? 'background:var(--primary-50)' : ''}">
                    <div style="font-weight:700;font-size:0.95rem;color:var(--gray-900);margin-bottom:4px">${rate.room_name}</div>
                    <div style="font-size:0.8rem;color:var(--gray-500);margin-bottom:var(--space-2)">🍽️ ${rate.meal_label}</div>
                    <div style="display:flex;align-items:center;gap:var(--space-2);margin-bottom:var(--space-3);flex-wrap:wrap">
                        ${rate.refundable ? '<span class="badge badge-green">✓ Cancelación gratis</span>' : '<span class="badge badge-red">No reembolsable</span>'}
                    </div>
                    <div style="display:flex;align-items:flex-end;justify-content:space-between">
                        <div>
                            <div style="font-size:1.4rem;font-weight:800;color:var(--primary-600)">$${(rate.daily_price || 0).toFixed(0)}<span style="font-size:0.8rem;color:var(--gray-400);font-weight:400">/noche</span></div>
                            <div style="font-size:0.8rem;color:var(--gray-400)">${nights} noches: $${(rate.total_price || 0).toFixed(0)}</div>
                        </div>
                        <button class="btn btn-primary btn-sm btn-rate-select" 
                                data-rate='${JSON.stringify(rate)}' 
                                data-hotel='${JSON.stringify(hotel)}'>Seleccionar</button>
                    </div>
                </div>`).join('');

            div.querySelectorAll('.btn-rate-select').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const rate = JSON.parse(btn.getAttribute('data-rate'));
                    const hotel = JSON.parse(btn.getAttribute('data-hotel'));
                    this.selectRate(rate, hotel, urlParams);
                });
            });
        }
    },

    selectRate(rate, hotel, urlParams) {
        const params = new URLSearchParams({
            book_hash: rate.book_hash,
            hotel_id: hotel.id,
            hotel_name: hotel.name,
            hotel_address: hotel.address || '',
            hotel_stars: hotel.stars || '',
            hotel_image: (hotel.images || [])[0] || '',
            check_in: urlParams.get('checkin'),
            check_out: urlParams.get('checkout'),
            guests: urlParams.get('adults') || 2,
            room_name: rate.room_name,
            total_price: rate.total_price,
            currency: rate.currency || 'USD',
            meal_label: rate.meal_label,
            refundable: rate.refundable ? '1' : '0',
            cancellation_policy: rate.cancellation_policy ? JSON.stringify(rate.cancellation_policy) : '',
        });

        // Redirect to booking page (requires auth, handled by Laravel)
        window.location.href = `/booking?${params.toString()}`;
    },

    renderError() {
        const loadingEl = document.getElementById('hotel-loading');
        if (loadingEl) {
            loadingEl.innerHTML = `
                <div class="empty-state" style="padding:var(--space-16)">
                    <div class="empty-state-icon">😕</div>
                    <h3 style="font-weight:700;margin-bottom:var(--space-2)">No se pudo cargar el hotel</h3>
                    <a href="javascript:history.back()" class="btn btn-primary btn-md">Volver</a>
                </div>`;
        }
    }
};
