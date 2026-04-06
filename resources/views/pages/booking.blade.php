@extends('layouts.app')

@section('content')
<div id="booking-page" style="background:var(--cr); min-height:100vh; padding:48px 20px 80px;">
    <div style="max-width:1000px; margin:0 auto;">
        
        <!-- Status Banner (Price changes, errors) -->
        <div id="prebook-status" style="display:none; padding:16px 20px; border-radius:16px; margin-bottom:24px; font-size:14px; animation:fadeUp 0.4s ease both;"></div>

        <div style="display:grid; grid-template-columns:1fr 360px; gap:40px; align-items:start;">
            
            <!-- LEFT: GUEST FORM -->
            <div class="reveal">
                <div class="sec-tag">Proceso de reserva</div>
                <h1 style="font-family:'Fraunces',serif; font-size:36px; font-weight:700; color:var(--g); margin-bottom:32px; letter-spacing:-1px;">Completa tu próximo nido</h1>

                <form id="booking-form" onsubmit="BookingModule.submitBooking(event, bookingParams)">
                    @csrf
                    
                    <!-- Guest Details Card -->
                    <div style="background:var(--wh); border-radius:24px; padding:32px; border:1px solid rgba(47,47,47,.06); box-shadow:var(--sh); margin-bottom:24px;">
                        <h3 style="font-family:'Fraunces',serif; font-size:20px; font-weight:600; margin-bottom:24px; display:flex; align-items:center; gap:10px;">
                            <span style="font-size:24px;">👤</span> Datos del huésped principal
                        </h3>
                        
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                            <div class="sfield">
                                <label>Nombre</label>
                                <input type="text" id="first_name" value="{{ Auth::user()->name ?? '' }}" required>
                            </div>
                            <div class="sfield">
                                <label>Apellido</label>
                                <input type="text" id="last_name" required>
                            </div>
                            <div class="sfield">
                                <label>Email de confirmación</label>
                                <input type="email" id="email" value="{{ Auth::user()->email ?? '' }}" required>
                            </div>
                            <div class="sfield">
                                <label>Teléfono móvil</label>
                                <input type="tel" id="phone" placeholder="+00 000 000 000">
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Submit -->
                    <div style="background:var(--wh); border-radius:24px; padding:32px; border:1px solid rgba(47,47,47,.06); box-shadow:var(--sh);">
                        <label style="display:flex; gap:12px; cursor:pointer; margin-bottom:24px;">
                            <input type="checkbox" required style="width:20px; height:20px; accent-color:var(--t); flex-shrink:0; margin-top:2px;">
                            <span style="font-size:13px; color:var(--gm); line-height:1.5;">
                                Acepto los <strong>Términos y Condiciones</strong> y la <strong>Política de Privacidad</strong> de Nestay. Entiendo que esta es una reserva con confirmación inmediata.
                            </span>
                        </label>

                        <button type="submit" id="submit-booking-btn" class="btn-primary" style="width:100%; padding:16px; font-size:15px; font-weight:700;">
                            Confirmar y reservar nido
                        </button>
                        
                        <div style="text-align:center; margin-top:16px; font-size:11px; color:var(--gl); display:flex; align-items:center; justify-content:center; gap:6px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            Transacción 100% segura y encriptada
                        </div>
                    </div>
                </form>
            </div>

            <!-- RIGHT: STICKY SUMMARY -->
            <aside style="position:sticky; top:100px;">
                <div style="background:var(--wh); border-radius:24px; overflow:hidden; border:1px solid rgba(47,47,47,.06); box-shadow:var(--shl);">
                    <!-- Hotel Image -->
                    <div style="height:160px; overflow:hidden; background:var(--g);">
                        <img id="summary-img" style="width:100%; height:100%; object-fit:cover;" alt="Hotel">
                    </div>

                    <div style="padding:28px;">
                        <div style="margin-bottom:20px;">
                            <div id="summary-stars" style="font-size:14px; color:var(--t); margin-bottom:4px;"></div>
                            <h4 id="summary-name" style="font-family:'Fraunces',serif; font-size:20px; font-weight:700; color:var(--g); line-height:1.2;"></h4>
                        </div>

                        <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:24px;">
                            <div style="display:flex; justify-content:space-between; font-size:13px; border-bottom:1px solid rgba(47,47,47,.06); padding-bottom:8px;">
                                <span style="color:var(--gl);">Check-in</span>
                                <strong id="summary-in">...</strong>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:13px; border-bottom:1px solid rgba(47,47,47,.06); padding-bottom:8px;">
                                <span style="color:var(--gl);">Check-out</span>
                                <strong id="summary-out">...</strong>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:13px; border-bottom:1px solid rgba(47,47,47,.06); padding-bottom:8px;">
                                <span style="color:var(--gl);">Huéspedes</span>
                                <strong id="summary-guests">...</strong>
                            </div>
                        </div>

                        <div style="background:var(--tp); border-radius:16px; padding:20px; text-align:center;">
                            <div style="font-size:12px; color:var(--td); font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Precio Final</div>
                            <div id="summary-price" style="font-family:'Fraunces',serif; font-size:32px; color:var(--t); font-weight:800; line-height:1;">$0</div>
                            <div id="summary-nights" style="font-size:11px; color:var(--td); opacity:0.7; margin-top:4px;">...</div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<script>
    const bookingParams = new URLSearchParams(window.location.search);

    document.addEventListener('DOMContentLoaded', () => {
        // Populate Summary
        const name = bookingParams.get('hotel_name');
        const img = bookingParams.get('hotel_image');
        const stars = parseInt(bookingParams.get('hotel_stars')) || 0;
        const checkin = bookingParams.get('check_in');
        const checkout = bookingParams.get('check_out');
        const guests = bookingParams.get('guests');
        const price = bookingParams.get('total_price');

        document.getElementById('summary-name').textContent = name;
        document.getElementById('summary-img').src = img;
        document.getElementById('summary-stars').textContent = '★'.repeat(stars);
        document.getElementById('summary-in').textContent = formatDate(checkin);
        document.getElementById('summary-out').textContent = formatDate(checkout);
        document.getElementById('summary-guests').textContent = `${guests} adultos`;
        document.getElementById('summary-price').textContent = `$${Math.round(price)}`;
        
        const nights = Math.ceil((new Date(checkout) - new Date(checkin)) / 86400000);
        document.getElementById('summary-nights').textContent = `${nights} noche${nights > 1 ? 's' : ''}`;

        // Validate Prebook
        BookingModule.prebookValidate(bookingParams.get('book_hash'));
    });
</script>
@endsection
