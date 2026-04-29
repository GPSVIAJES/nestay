@extends('layouts.dashboard')

@section('dashboard_content')
    <div style="margin-bottom:40px;">
        <span style="font-size:12px; font-weight:800; text-transform:uppercase; color:var(--t); letter-spacing:1.5px; background:var(--tp); padding:6px 14px; border-radius:100px; border:1px solid var(--tl);">Resumen de tu cuenta</span>
        <h1 style="font-size:48px; font-weight:800; color:var(--v); letter-spacing:-2px; margin-top:16px; line-height:1;">Hola de nuevo, {{ explode(' ', Auth::user()->name)[0] }}</h1>
    </div>

    <!-- STATS GRID -->
    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:24px; margin-bottom:48px;">
        <div style="background:var(--wh); border-radius:32px; padding:32px; border:1px solid rgba(0,0,0,0.05); box-shadow:var(--sh); transition:transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size:40px; font-weight:800; color:var(--t); letter-spacing:-1px;">{{ $upcomingCount }}</div>
            <div style="font-size:13px; color:var(--gm); font-weight:700; text-transform:uppercase; opacity:0.6; margin-top:4px;">Próximos viajes</div>
        </div>
        <div style="background:var(--wh); border-radius:32px; padding:32px; border:1px solid rgba(0,0,0,0.05); box-shadow:var(--sh); transition:transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size:40px; font-weight:800; color:var(--v); letter-spacing:-1px;">{{ $totalBookings }}</div>
            <div style="font-size:13px; color:var(--gm); font-weight:700; text-transform:uppercase; opacity:0.6; margin-top:4px;">Reservas totales</div>
        </div>
        <div style="background:var(--wh); border-radius:32px; padding:32px; border:1px solid rgba(0,0,0,0.05); box-shadow:var(--sh); transition:transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size:40px; font-weight:800; color:var(--g); letter-spacing:-1px;">{{ number_format($totalBookings * 125, 0) }}</div>
            <div style="font-size:13px; color:var(--gm); font-weight:700; text-transform:uppercase; opacity:0.6; margin-top:4px;">Nidos puntos</div>
        </div>
    </div>

    <!-- RECENT ACTIVITY -->
    <section>
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:24px;">
            <h2 style="font-size:28px; font-weight:800; color:var(--v); letter-spacing:-1px;">Tus próximos viajes</h2>
            <a href="{{ route('dashboard.bookings') }}" style="font-size:14px; color:var(--t); font-weight:700; text-decoration:none; display:flex; align-items:center; gap:6px;">Ver todos los viajes <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14m-7-7 7 7-7 7"/></svg></a>
        </div>

        @if($upcomingBookings->isEmpty())
            <div style="background:rgba(255,255,255,0.4); border-radius:32px; padding:64px; text-align:center; border:2px dashed rgba(0,0,0,0.08);">
                <div style="font-size:48px; margin-bottom:20px;">🕊️</div>
                <h3 style="font-size:22px; font-weight:700; color:var(--v); margin-bottom:8px;">No tienes planes por ahora</h3>
                <p style="color:var(--gm); font-size:15px; margin-bottom:32px; max-width:300px; margin-left:auto; margin-right:auto;">El mundo está esperando. ¿Buscamos tu próximo nido?</p>
                <a href="{{ route('home') }}" class="btn-primary" style="display:inline-flex; align-items:center; gap:10px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    Buscar hoteles ahora
                </a>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:16px;">
                @foreach($upcomingBookings as $booking)
                    <div style="background:var(--wh); border-radius:24px; padding:16px; border:1px solid rgba(0,0,0,0.05); box-shadow:var(--sh); display:grid; grid-template-columns:100px 1fr 140px; align-items:center; transition:all 0.3s;">
                        <div style="height:80px; width:100%; border-radius:16px; background-image:url('{{ $booking->hotel_image }}'); background-size:cover; background-position:center; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"></div>
                        <div style="padding:0 24px;">
                            <h4 style="font-size:18px; font-weight:800; color:var(--v); margin-bottom:4px;">{{ $booking->hotel_name }}</h4>
                            <div style="font-size:13px; color:var(--gm); display:flex; align-items:center; gap:6px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                {{ $booking->check_in->format('d M') }} — {{ $booking->check_out->format('d M, Y') }}
                            </div>
                        </div>
                        <div style="text-align:right; padding-right:8px;">
                            <div style="font-size:22px; font-weight:800; color:var(--t); letter-spacing:-1px;">$ {{ number_format($booking->total_price, 0) }}</div>
                            <span style="font-size:10px; font-weight:800; color:var(--g); background:#E8F3EE; padding:4px 10px; border-radius:100px; text-transform:uppercase; margin-top:4px; display:inline-block;">{{ $booking->status_label }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- EXPLORE CTA -->
    <div style="margin-top:56px; background:var(--v); border-radius:32px; padding:48px; display:flex; justify-content:space-between; align-items:center; color:#fff; position:relative; overflow:hidden;">
        <div style="position:relative; z-index:2; max-width:400px;">
            <h3 style="font-size:32px; font-weight:800; margin-bottom:12px; letter-spacing:-1.5px;">¿Cuál será tu próximo nido?</h3>
            <p style="font-size:16px; opacity:0.7; margin-bottom:32px;">Tenemos más de 2.4 millones de alojamientos esperándote para tu próxima aventura.</p>
            <a href="{{ route('home') }}" class="btn-primary" style="padding:14px 32px; font-size:16px;">Explorar destinos</a>
        </div>
        <div style="font-size:140px; opacity:0.05; position:absolute; right:40px; bottom:-20px; z-index:1; transform: rotate(-15deg);">🏨</div>
        
        <!-- Decoration circles -->
        <div style="position:absolute; top:-50px; right:-50px; width:200px; height:200px; border-radius:50%; background:var(--t); opacity:0.1; filter:blur(40px);"></div>
    </div>
@endsection
