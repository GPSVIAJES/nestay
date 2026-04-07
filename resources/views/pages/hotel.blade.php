@extends('layouts.app')

@section('content')
<div id="hotel-page" style="background:var(--wh); min-height:100vh;">
    <!-- Loading State -->
    <div id="hotel-loading" style="padding:100px 20px; text-align:center;">
        <div class="l-spinner" style="margin:0 auto 20px;"></div>
        <div style="font-family:'Fraunces',serif; font-size:24px; color:var(--g);">Abriendo el nido...</div>
    </div>

    <!-- Actual Content -->
    <div id="hotel-content" style="display:none; animation:fadeIn 0.5s ease both;">
        
        <!-- HEADER / GALLERY -->
        <div class="hotel-hero" style="height:450px; position:relative; overflow:hidden; background:var(--g);">
            <div id="gallery-slides" style="display:flex; height:100%; transition:transform 0.6s cubic-bezier(.22,1,.36,1);"></div>
            <div style="position:absolute; bottom:24px; left:32px; right:32px; display:flex; justify-content:space-between; align-items:flex-end; z-index:10;">
                <div style="color:#fff; text-shadow:0 2px 10px rgba(0,0,0,0.3);">
                    <div id="hotel-stars" style="color:var(--t); font-size:18px; margin-bottom:8px;"></div>
                    <h1 id="hotel-name" style="font-family:'Fraunces',serif; font-size:44px; font-weight:700; line-height:1; letter-spacing:-1.2px; margin-bottom:8px;"></h1>
                    <div id="hotel-loc" style="font-size:14px; opacity:0.85; display:flex; align-items:center; gap:6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span></span>
                    </div>
                </div>
                <div id="gallery-controls" style="display:flex; gap:10px;">
                    <button onclick="prevSlide()" class="btn-ghost" style="background:rgba(255,255,255,0.15); border-color:rgba(255,255,255,0.3); color:#fff; width:44px; height:44px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:50%; font-size:20px;">‹</button>
                    <button onclick="nextSlide()" class="btn-ghost" style="background:rgba(255,255,255,0.15); border-color:rgba(255,255,255,0.3); color:#fff; width:44px; height:44px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:50%; font-size:20px;">›</button>
                </div>
            </div>
            <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 60%); pointer-events:none;"></div>
        </div>

        <!-- TWO COLUMN LAYOUT -->
        <div style="max-width:1200px; margin:0 auto; padding:48px 32px 80px; display:grid; grid-template-columns:1fr 380px; gap:48px;">
            
            <!-- LEFT: DETAILS -->
            <div>
                <section style="margin-bottom:48px;">
                    <div class="sec-tag">Sobre este nido</div>
                    <p id="hotel-desc" style="font-size:16px; color:var(--gm); line-height:1.7; font-weight:300;"></p>
                </section>

                <section style="margin-bottom:48px;">
                    <div class="sec-tag">Servicios y Amenidades</div>
                    <div id="hotel-amenities" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:12px; margin-top:16px;"></div>
                </section>

                <section>
                    <div class="sec-tag">Políticas del alojamiento</div>
                    <div id="hotel-policies" style="margin-top:16px; display:flex; flex-direction:column; gap:10px;"></div>
                </section>
            </div>

            <!-- RIGHT: STICKY PRICE BOX / RATES -->
            <aside style="position:sticky; top:100px; height:fit-content;">
                <div style="background:var(--wh); border-radius:24px; padding:32px; border:1px solid rgba(47,47,47,.06); box-shadow:var(--shl);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                        <div>
                            <div style="font-size:12px; color:var(--gl); text-transform:uppercase; letter-spacing:1px; font-weight:700;">Desde</div>
                            <div class="mprice" style="font-family:'Fraunces',serif; font-size:40px; color:var(--t); line-height:1;" id="min-price">$0</div>
                        </div>
                        <div id="rating-badge" class="badge badge-g" style="font-size:14px; padding:8px 16px;"></div>
                    </div>

                    <div style="background:var(--cr); border-radius:16px; padding:16px; margin-bottom:24px;">
                        <div style="display:flex; justify-content:space-between; font-size:13px; color:var(--gm); margin-bottom:4px;">
                            <span>Fechas</span>
                            <strong id="dates-summary">...</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:13px; color:var(--gm);">
                            <span>Huéspedes</span>
                            <strong id="guests-summary">...</strong>
                        </div>
                    </div>

                    <div id="rate-list" style="display:flex; flex-direction:column; gap:16px;">
                        <!-- Rates will be rendered here -->
                    </div>

                    <div class="msafe" style="margin-top:24px; font-size:12px; color:var(--gl); display:flex; align-items:center; justify-content:center; gap:6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Reserva 100% segura y garantizada
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<script>
    const hotelPage = {
        id: '{{ $id }}',
        currentSlide: 0,
        data: null,

        async init() {
            const params = new URLSearchParams(window.location.search);
            try {
                const response = await NestayAPI.getHotelDetails({
                    hotel_id:  this.id,
                    checkin:   params.get('check_in'),
                    checkout:  params.get('check_out'),
                    adults:    parseInt(params.get('adults')) || 2,
                });
                this.data = response.data;
                this.render();
            } catch (e) {
                console.error(e);
            }
        },

        render() {
            const { hotel, rates } = this.data;
            document.getElementById('hotel-loading').style.display = 'none';
            const content = document.getElementById('hotel-content');
            content.style.display = 'block';

            // Text content
            document.getElementById('hotel-name').textContent = hotel.name;
            document.getElementById('hotel-loc').querySelector('span').textContent = hotel.address || hotel.city;
            document.getElementById('hotel-stars').textContent = '★'.repeat(hotel.stars || 0);
            document.getElementById('hotel-desc').textContent = hotel.description || 'Este alojamiento no tiene una descripción detallada todavía.';
            document.getElementById('rating-badge').textContent = `⭐ ${hotel.rating || 'Nuevo'}`;
            
            const minPrice = rates.reduce((min, r) => Math.min(min, r.daily_price), rates[0]?.daily_price || 0);
            document.getElementById('min-price').textContent = `$${Math.round(minPrice)}`;

            const params = new URLSearchParams(window.location.search);
            document.getElementById('dates-summary').textContent = `${params.get('check_in')} – ${params.get('check_out')}`;
            document.getElementById('guests-summary').textContent = `${params.get('adults')} adultos`;

            // Gallery
            const slides = document.getElementById('gallery-slides');
            slides.innerHTML = hotel.images.map(img => `
                <div style="flex-shrink:0; width:100%; height:450px;">
                    <img src="${img}" style="width:100%; height:100%; object-fit:cover;">
                </div>
            `).join('');

            // Amenities
            const amenDiv = document.getElementById('hotel-amenities');
            amenDiv.innerHTML = Object.keys(hotel.amenities || {}).slice(0,10).map(a => `
                <div class="amen-item" style="padding:10px; border-radius:12px; background:var(--cr); display:flex; align-items:center; gap:10px; font-size:13px;">
                    <span style="font-size:16px;">✦</span> ${a.replace('_', ' ')}
                </div>
            `).join('');

            // Rates
            const rateList = document.getElementById('rate-list');
            rateList.innerHTML = rates.map(r => `
                <div style="padding:20px; border-radius:18px; border:1.5px solid rgba(47,47,47,.08); background:var(--wh); transition:border-color .2s;">
                    <div style="font-weight:700; font-size:14px; margin-bottom:4px;">${r.room_name}</div>
                    <div style="font-size:12px; color:var(--gl); margin-bottom:12px;">${r.meal_label}</div>
                    <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                        <div>
                            <div style="font-size:18px; font-weight:700; color:var(--g);">$${Math.round(r.total_price)}</div>
                            <div style="font-size:11px; color:var(--gl);">precio total</div>
                        </div>
                        <button onclick='hotelPage.selectRate(${JSON.stringify(r).replace(/'/g, "&apos;")})' class="btn-primary" style="padding:8px 16px; font-size:12px;">Seleccionar</button>
                    </div>
                </div>
            `).join('');
        },

        selectRate(rate) {
            const h = this.data.hotel;
            const params = new URLSearchParams(window.location.search);
            const q = new URLSearchParams({
                book_hash: rate.book_hash,
                hotel_id: h.id,
                hotel_name: h.name,
                hotel_image: h.images[0],
                check_in: params.get('check_in'),
                check_out: params.get('check_out'),
                guests: params.get('adults'),
                total_price: rate.total_price
            });
            window.location.href = '/booking?' + q.toString();
        }
    };

    function nextSlide() {
        const slides = document.getElementById('gallery-slides');
        const count = slides.children.length;
        hotelPage.currentSlide = (hotelPage.currentSlide + 1) % count;
        slides.style.transform = `translateX(-${hotelPage.currentSlide * 100}%)`;
    }
    function prevSlide() {
        const slides = document.getElementById('gallery-slides');
        const count = slides.children.length;
        hotelPage.currentSlide = (hotelPage.currentSlide - 1 + count) % count;
        slides.style.transform = `translateX(-${hotelPage.currentSlide * 100}%)`;
    }

    document.addEventListener('DOMContentLoaded', () => hotelPage.init());
</script>
@endsection
