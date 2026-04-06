@extends('layouts.app')

@section('content')
<div id="confirm-page" style="background:var(--cr); min-height:100vh; padding:80px 20px; display:flex; align-items:flex-start; justify-content:center;">
    <div style="max-width:600px; width:100%; text-align:center; animation:fadeUp 0.6s ease both;">
        
        <!-- SUCCESS ICON -->
        <div style="width:80px; height:80px; background:var(--vp); border-radius:50%; margin:0 auto 24px; display:flex; align-items:center; justify-content:center; box-shadow:0 10px 30px rgba(129,178,154,0.2);">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--vd)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </div>

        <h1 style="font-family:'Fraunces',serif; font-size:42px; font-weight:700; color:var(--g); margin-bottom:12px; letter-spacing:-1.2px;">¡Tu nido está listo!</h1>
        <p style="font-size:16px; color:var(--gm); margin-bottom:40px; font-weight:300;">Hemos enviado la confirmación instantánea a <strong style="color:var(--t)">{{ $booking->guest_email }}</strong></p>

        <!-- BOOKING CARD -->
        <div style="background:var(--wh); border-radius:28px; overflow:hidden; border:1px solid rgba(47,47,47,.06); box-shadow:var(--shl); text-align:left; margin-bottom:32px;">
            @if($booking->hotel_image)
            <div style="height:220px; overflow:hidden;">
                <img src="{{ $booking->hotel_image }}" style="width:100%; height:100%; object-fit:cover;" alt="Hotel">
            </div>
            @endif

            <div style="padding:32px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px;">
                    <div>
                        <div style="font-size:13px; color:var(--t); margin-bottom:4px;">{{ str_repeat('★', (int)$booking->hotel_stars) }}</div>
                        <h4 style="font-family:'Fraunces',serif; font-size:22px; font-weight:700; color:var(--g); line-height:1.2;">{{ $booking->hotel_name }}</h4>
                        <p style="font-size:13px; color:var(--gl); margin-top:4px;">{{ $booking->hotel_address }}</p>
                    </div>
                    <span class="badge badge-g" style="padding:6px 14px; font-size:12px;">✓ Confirmada</span>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; padding:20px; background:var(--cr); border-radius:20px;">
                    <div>
                        <div style="font-size:10px; font-weight:700; color:var(--gl); text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Localizador</div>
                        <div style="font-weight:700; color:var(--t); font-size:16px;">#{{ $booking->ratehawk_order_id }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px; font-weight:700; color:var(--gl); text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Check-in</div>
                        <div style="font-weight:600; color:var(--g);">{{ $booking->check_in->format('d M, Y') }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px; font-weight:700; color:var(--gl); text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Check-out</div>
                        <div style="font-weight:600; color:var(--g);">{{ $booking->check_out->format('d M, Y') }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px; font-weight:700; color:var(--gl); text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">Precio Total</div>
                        <div style="font-weight:800; color:var(--g);">$ {{ number_format($booking->total_price, 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACTIONS -->
        <div style="display:flex; gap:16px;">
            <a href="{{ route('dashboard.bookings') }}" class="btn-primary" style="flex:1; padding:16px; font-size:14px; font-weight:700; text-align:center;">Gestionar mis reservas</a>
            <a href="{{ route('home') }}" class="btn-ghost" style="flex:1; padding:16px; font-size:14px; font-weight:700; text-align:center;">Nueva búsqueda</a>
        </div>
    </div>
</div>
@endsection
