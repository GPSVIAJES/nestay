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
                <a href="{{ route('dashboard') }}" style="display:flex; align-items:center; gap:10px; padding:12px; border-radius:12px; font-size:13px; color:var(--gm); transition:all .2s;">
                    🏠 Dashboard
                </a>
                <a href="{{ route('dashboard.bookings') }}" style="display:flex; align-items:center; gap:10px; padding:12px; border-radius:12px; font-size:13px; font-weight:700; color:var(--t); background:var(--tp); transition:all .2s;">
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
                <div class="sec-tag">Tu historial de nidos</div>
                <h1 style="font-family:'Fraunces',serif; font-size:36px; font-weight:700; color:var(--g); letter-spacing:-1px;">Mis Reservas</h1>
            </div>

            <!-- TABS -->
            <div style="display:flex; gap:24px; margin-bottom:24px; border-bottom:1px solid rgba(47,47,47,.07);">
                <button onclick="switchTab('upcoming')" id="tab-upcoming" class="active" style="padding:12px 0; font-size:14px; font-weight:600; color:var(--t); border-bottom:2px solid var(--t); background:none; cursor:pointer;">Próximas</button>
                <button onclick="switchTab('past')" id="tab-past" style="padding:12px 0; font-size:14px; font-weight:600; color:var(--gl); border-bottom:2px solid transparent; background:none; cursor:pointer;">Pasadas</button>
            </div>

            <!-- UPCOMING CONTENT -->
            <div id="content-upcoming">
                @if($upcoming->isEmpty())
                    <div style="background:var(--wh); border-radius:24px; padding:60px 40px; text-align:center; border:1px dashed rgba(47,47,47,.12);">
                        <div style="font-size:48px; margin-bottom:16px;">🕊️</div>
                        <h3 style="font-family:'Fraunces',serif; font-size:22px; color:var(--g);">Aún no tienes nidos reservados</h3>
                        <p style="color:var(--gm); margin-bottom:24px; font-weight:300;">Explora el mundo y encuentra tu próximo lugar perfecto.</p>
                        <a href="{{ route('home') }}" class="btn-primary" style="padding:12px 32px; display:inline-block;">Buscar hoteles ahora</a>
                    </div>
                @else
                    <div style="display:flex; flex-direction:column; gap:16px;">
                        @foreach($upcoming as $booking)
                            <div class="rcard" style="display:grid; grid-template-columns:140px 1fr 180px; align-items:center; grid-template-rows: auto;">
                                <div class="rcard-img" style="height:100%; min-height:120px; background-image:url('{{ $booking->hotel_image }}'); background-size:cover; background-position:center; border-radius:0;"></div>
                                <div style="padding:20px;">
                                    <h4 style="font-family:'Fraunces',serif; font-size:18px; font-weight:600; color:var(--g); margin-bottom:4px;">{{ $booking->hotel_name }}</h4>
                                    <div style="font-size:12px; color:var(--gl); display:flex; align-items:center; gap:6px; margin-bottom:12px;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        {{ $booking->hotel_address }}
                                    </div>
                                    <div style="display:flex; gap:20px;">
                                        <div>
                                            <div style="font-size:9px; font-weight:700; color:var(--gl); text-transform:uppercase; letter-spacing:1px;">Check-in</div>
                                            <div style="font-size:13px; font-weight:600; color:var(--gm);">{{ $booking->check_in->format('d M') }}</div>
                                        </div>
                                        <div>
                                            <div style="font-size:9px; font-weight:700; color:var(--gl); text-transform:uppercase; letter-spacing:1px;">Check-out</div>
                                            <div style="font-size:13px; font-weight:600; color:var(--gm);">{{ $booking->check_out->format('d M') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div style="padding:20px; text-align:right; border-left:1px solid rgba(47,47,47,.06);">
                                    <span class="badge badge-g" style="margin-bottom:12px;">{{ $booking->status_label }}</span>
                                    <div style="font-family:'Fraunces',serif; font-size:22px; font-weight:700; color:var(--t); line-height:1;">${{ number_format($booking->total_price, 0) }}</div>
                                    <div style="font-size:11px; color:var(--gl); margin-bottom:12px;">{{ $booking->nights }} nits</div>
                                    <a href="{{ route('booking.confirm', $booking->ratehawk_order_id) }}" style="font-size:12px; font-weight:700; color:var(--t); text-decoration:underline;">Ver bono</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- PAST CONTENT -->
            <div id="content-past" style="display:none;">
                @if($past->isEmpty())
                    <div style="background:var(--wh); border-radius:24px; padding:60px 40px; text-align:center; border:1px dashed rgba(47,47,47,.12); opacity:0.6;">
                        <h3 style="font-family:'Fraunces',serif; font-size:22px; color:var(--g);">Nada por aquí aún</h3>
                        <p style="color:var(--gm); font-weight:300;">Tus nidos anteriores aparecerán en esta lista.</p>
                    </div>
                @else
                    <div style="display:flex; flex-direction:column; gap:16px;">
                        @foreach($past as $booking)
                            <div class="rcard" style="display:grid; grid-template-columns:100px 1fr 140px; align-items:center; opacity:0.7;">
                                <img src="{{ $booking->hotel_image }}" style="width:100%; height:100px; object-fit:cover; filter:grayscale(1);">
                                <div style="padding:16px;">
                                    <h4 style="font-size:15px; font-weight:600; color:var(--g);">{{ $booking->hotel_name }}</h4>
                                    <div style="font-size:12px; color:var(--gl);">{{ $booking->check_in->format('M Y') }} · ${{ number_format($booking->total_price, 0) }}</div>
                                </div>
                                <div style="padding:16px; text-align:right;">
                                    <span class="badge" style="background:#eee; color:#777; font-size:10px;">Completada</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>

<script>
function switchTab(tab) {
    document.getElementById('content-upcoming').style.display = tab === 'upcoming' ? 'block' : 'none';
    document.getElementById('content-past').style.display = tab === 'past' ? 'block' : 'none';
    
    document.getElementById('tab-upcoming').style.color = tab === 'upcoming' ? 'var(--t)' : 'var(--gl)';
    document.getElementById('tab-upcoming').style.borderBottomColor = tab === 'upcoming' ? 'var(--t)' : 'transparent';
    
    document.getElementById('tab-past').style.color = tab === 'past' ? 'var(--t)' : 'var(--gl)';
    document.getElementById('tab-past').style.borderBottomColor = tab === 'past' ? 'var(--t)' : 'transparent';
}
</script>
@endsection
