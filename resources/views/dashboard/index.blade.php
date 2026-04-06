@extends('layouts.app')

@section('content')
<div id="dashboard-page" style="background:var(--cr); min-height:100vh; padding:48px 20px 80px;">
    <div style="max-width:1100px; margin:0 auto; display:grid; grid-template-columns:240px 1fr; gap:40px; align-items:start;">
        
        <!-- SIDEBAR -->
        <aside class="reveal" style="background:var(--wh); border-radius:24px; padding:32px; border:1px solid rgba(47,47,47,.06); box-shadow:var(--sh); position:sticky; top:100px;">
            <div style="text-align:center; margin-bottom:32px;">
                <div style="width:64px; height:64px; background:var(--t); color:#fff; border-radius:50%; margin:0 auto 12px; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:700;">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div style="font-family:'Fraunces',serif; font-size:18px; font-weight:700; color:var(--g);">{{ Auth::user()->name }}</div>
                <div style="font-size:11px; color:var(--gl);">Viajero Nestay de Plata</div>
            </div>

            <nav style="display:flex; flex-direction:column; gap:8px;">
                <a href="{{ route('dashboard') }}" style="display:flex; align-items:center; gap:10px; padding:12px; border-radius:12px; font-size:13px; font-weight:700; color:var(--t); background:var(--tp); transition:all .2s;">
                    🏠 Dashboard
                </a>
                <a href="{{ route('dashboard.bookings') }}" style="display:flex; align-items:center; gap:10px; padding:12px; border-radius:12px; font-size:13px; color:var(--gm); transition:all .2s;">
                    📔 Mis Reservas
                </a>
                <a href="{{ route('profile.edit') }}" style="display:flex; align-items:center; gap:10px; padding:12px; border-radius:12px; font-size:13px; color:var(--gm); transition:all .2s;">
                    👤 Mi Perfil
                </a>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="reveal d1">
            <div style="margin-bottom:32px;">
                <div class="sec-tag">Resumen de tu cuenta</div>
                <h1 style="font-family:'Fraunces',serif; font-size:36px; font-weight:700; color:var(--g); letter-spacing:-1px;">Hola de nuevo, {{ explode(' ', Auth::user()->name)[0] }}</h1>
            </div>

            <!-- STATS GRID -->
            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:20px; margin-bottom:40px;">
                <div style="background:var(--wh); border-radius:24px; padding:24px; border:1px solid rgba(47,47,47,.06); box-shadow:var(--sh);">
                    <div style="font-size:32px; font-weight:800; color:var(--t); font-family:'Fraunces',serif;">{{ $upcomingCount }}</div>
                    <div style="font-size:12px; color:var(--gl); font-weight:600; text-transform:uppercase;">Próximos viajes</div>
                </div>
                <div style="background:var(--wh); border-radius:24px; padding:24px; border:1px solid rgba(47,47,47,.06); box-shadow:var(--sh);">
                    <div style="font-size:32px; font-weight:800; color:var(--vd); font-family:'Fraunces',serif;">{{ $totalBookings }}</div>
                    <div style="font-size:12px; color:var(--gl); font-weight:600; text-transform:uppercase;">Reservas totales</div>
                </div>
                <div style="background:var(--wh); border-radius:24px; padding:24px; border:1px solid rgba(47,47,47,.06); box-shadow:var(--sh);">
                    <div style="font-size:32px; font-weight:800; color:var(--g); font-family:'Fraunces',serif;">{{ number_format($totalBookings * 125, 0) }}</div>
                    <div style="font-size:12px; color:var(--gl); font-weight:600; text-transform:uppercase;">Nidos puntos</div>
                </div>
            </div>

            <!-- RECENT ACTIVITY -->
            <section>
                <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:20px;">
                    <h2 style="font-family:'Fraunces',serif; font-size:24px; color:var(--g);">Tus próximos viajes</h2>
                    <a href="{{ route('dashboard.bookings') }}" style="font-size:13px; color:var(--t); font-weight:700;">Ver todos los viajes →</a>
                </div>

                @if($upcomingBookings->isEmpty())
                    <div style="background:rgba(255,255,255,0.4); border-radius:24px; padding:48px; text-align:center; border:1px dashed rgba(47,47,47,.1);">
                        <p style="color:var(--gl); font-size:14px;">No tienes planes por ahora. ¿Buscamos algo?</p>
                    </div>
                @else
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        @foreach($upcomingBookings as $booking)
                            <div class="rcard" style="display:grid; grid-template-columns:100px 1fr 140px; align-items:center;">
                                <div class="rcard-img" style="height:80px; width:100%; min-height:0; background-image:url('{{ $booking->hotel_image }}');"></div>
                                <div style="padding:16px;">
                                    <h4 style="font-size:15px; font-weight:700; color:var(--g); margin-bottom:2px;">{{ $booking->hotel_name }}</h4>
                                    <div style="font-size:12px; color:var(--gl);">{{ $booking->check_in->format('d M') }} — {{ $booking->check_out->format('d M, Y') }}</div>
                                </div>
                                <div style="padding:16px; text-align:right;">
                                    <div style="font-weight:800; color:var(--t);">$ {{ number_format($booking->total_price, 0) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <!-- EXPLORE CTA -->
            <div style="margin-top:40px; background:var(--g); border-radius:24px; padding:40px; display:flex; justify-content:space-between; align-items:center; color:#fff; position:relative; overflow:hidden;">
                <div style="position:relative; z-index:2;">
                    <h3 style="font-family:'Fraunces',serif; font-size:28px; margin-bottom:8px;">¿Cuál será tu próximo nido?</h3>
                    <p style="font-size:14px; opacity:0.7; margin-bottom:24px;">Tenemos 2.4M+ de alojamientos esperándote.</p>
                    <a href="{{ route('home') }}" class="btn-primary" style="padding:12px 24px;">Explorar destinos</a>
                </div>
                <div style="font-size:80px; opacity:0.1; position:absolute; right:20px; bottom:-10px; z-index:1;">🏨</div>
            </div>
        </main>
    </div>
</div>
@endsection
