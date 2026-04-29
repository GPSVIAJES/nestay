@extends('layouts.dashboard')

@section('dashboard_content')
    <div style="margin-bottom:40px;">
        <span style="font-size:12px; font-weight:800; text-transform:uppercase; color:var(--t); letter-spacing:1.5px; background:var(--tp); padding:6px 14px; border-radius:100px; border:1px solid var(--tl);">Tu historial de nidos</span>
        <h1 style="font-size:48px; font-weight:800; color:var(--v); letter-spacing:-2px; margin-top:16px; line-height:1;">Mis Reservas</h1>
    </div>

    <!-- TABS -->
    <div style="display:flex; gap:32px; margin-bottom:32px; border-bottom:1px solid rgba(0,0,0,0.05);">
        <button onclick="switchTab('upcoming')" id="tab-upcoming" class="active" 
                style="padding:16px 0; font-size:15px; font-weight:700; color:var(--t); border-bottom:3px solid var(--t); background:none; cursor:pointer; transition:all .3s; outline:none;">
            Próximas
        </button>
        <button onclick="switchTab('past')" id="tab-past" 
                style="padding:16px 0; font-size:15px; font-weight:700; color:var(--gm); opacity:0.5; border-bottom:3px solid transparent; background:none; cursor:pointer; transition:all .3s; outline:none;">
            Pasadas
        </button>
    </div>

    <!-- UPCOMING CONTENT -->
    <div id="content-upcoming">
        @if($upcoming->isEmpty())
            <div style="background:var(--wh); border-radius:32px; padding:80px 40px; text-align:center; border:2px dashed rgba(0,0,0,0.08);">
                <div style="font-size:56px; margin-bottom:24px;">🕊️</div>
                <h3 style="font-size:24px; font-weight:800; color:var(--v); margin-bottom:12px;">Aún no tienes nidos reservados</h3>
                <p style="color:var(--gm); margin-bottom:32px; font-weight:400; max-width:320px; margin-left:auto; margin-right:auto; opacity:0.7;">Tu historial de viajes está esperando a ser escrito. ¿Comenzamos hoy?</p>
                <a href="{{ route('home') }}" class="btn-primary" style="padding:14px 40px; font-size:16px; display:inline-flex; align-items:center; gap:10px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Explorar destinos
                </a>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:20px;">
                @foreach($upcoming as $booking)
                    <div style="background:var(--wh); border-radius:32px; overflow:hidden; border:1px solid rgba(0,0,0,0.05); box-shadow:var(--sh); display:grid; grid-template-columns:200px 1fr 220px; align-items:stretch; transition:transform 0.3s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="background-image:url('{{ $booking->hotel_image }}'); background-size:cover; background-position:center;"></div>
                        <div style="padding:32px;">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                                <span style="font-size:10px; font-weight:800; color:var(--t); background:var(--tp); padding:4px 10px; border-radius:100px; text-transform:uppercase; border:1px solid var(--tl);">Reserva #{{ substr($booking->ratehawk_order_id, 0, 8) }}</span>
                            </div>
                            <h4 style="font-size:22px; font-weight:800; color:var(--v); margin-bottom:8px; letter-spacing:-0.5px;">{{ $booking->hotel_name }}</h4>
                            <div style="font-size:13px; color:var(--gm); display:flex; align-items:center; gap:8px; margin-bottom:20px; opacity:0.7;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ $booking->hotel_address }}
                            </div>
                            
                            <div style="display:flex; gap:32px; background:var(--cr); padding:16px 20px; border-radius:20px; width:fit-content;">
                                <div>
                                    <div style="font-size:10px; font-weight:800; color:var(--gm); text-transform:uppercase; letter-spacing:1px; opacity:0.5;">Check-in</div>
                                    <div style="font-size:15px; font-weight:700; color:var(--v);">{{ $booking->check_in->format('d M, Y') }}</div>
                                </div>
                                <div style="width:1px; background:rgba(0,0,0,0.05);"></div>
                                <div>
                                    <div style="font-size:10px; font-weight:800; color:var(--gm); text-transform:uppercase; letter-spacing:1px; opacity:0.5;">Check-out</div>
                                    <div style="font-size:15px; font-weight:700; color:var(--v);">{{ $booking->check_out->format('d M, Y') }}</div>
                                </div>
                            </div>
                        </div>
                        <div style="padding:32px; text-align:right; border-left:1px solid rgba(0,0,0,0.05); display:flex; flex-direction:column; justify-content:center; align-items:flex-end; background:#fafaf8;">
                            <div style="font-size:32px; font-weight:800; color:var(--t); line-height:1; letter-spacing:-1px;">${{ number_format($booking->total_price, 0) }}</div>
                            <div style="font-size:12px; color:var(--gm); margin-top:4px; font-weight:500; opacity:0.6;">{{ $booking->nights }} noches · {{ $booking->guests_count }} personas</div>
                            
                            <div style="margin-top:24px; display:flex; flex-direction:column; gap:10px; width:100%;">
                                <a href="{{ route('booking.confirm', $booking->ratehawk_order_id) }}" class="btn-primary" style="padding:10px 20px; font-size:13px; width:100%; text-decoration:none;">Ver Detalles</a>
                                <span style="font-size:11px; font-weight:800; color:var(--g); text-align:center; text-transform:uppercase;">{{ $booking->status_label }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div style="margin-top:40px;">
                {{ $upcoming->links() }}
            </div>
        @endif
    </div>

    <!-- PAST CONTENT -->
    <div id="content-past" style="display:none;">
        @if($past->isEmpty())
            <div style="background:var(--wh); border-radius:32px; padding:80px 40px; text-align:center; border:1px dashed rgba(0,0,0,0.08); opacity:0.6;">
                <h3 style="font-size:24px; font-weight:800; color:var(--v);">Nada por aquí aún</h3>
                <p style="color:var(--gm); font-weight:400; opacity:0.7;">Tus nidos anteriores aparecerán en esta lista una vez que hayas completado tus viajes.</p>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:16px;">
                @foreach($past as $booking)
                    <div style="background:var(--wh); border-radius:24px; padding:20px; border:1px solid rgba(0,0,0,0.05); display:grid; grid-template-columns:100px 1fr 140px; align-items:center; opacity:0.6;">
                        <div style="height:80px; width:100%; border-radius:16px; background-image:url('{{ $booking->hotel_image }}'); background-size:cover; background-position:center; filter:grayscale(0.5);"></div>
                        <div style="padding:0 24px;">
                            <h4 style="font-size:18px; font-weight:800; color:var(--v); margin-bottom:4px;">{{ $booking->hotel_name }}</h4>
                            <div style="font-size:13px; color:var(--gm);">{{ $booking->check_in->format('M Y') }} · ${{ number_format($booking->total_price, 0) }}</div>
                        </div>
                        <div style="text-align:right; padding-right:8px;">
                            <span style="font-size:10px; font-weight:800; color:var(--gm); background:#eee; padding:4px 10px; border-radius:100px; text-transform:uppercase;">Completada</span>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div style="margin-top:40px;">
                {{ $past->links() }}
            </div>
        @endif
    </div>

    <script>
    function switchTab(tab) {
        document.getElementById('content-upcoming').style.display = tab === 'upcoming' ? 'block' : 'none';
        document.getElementById('content-past').style.display = tab === 'past' ? 'block' : 'none';
        
        const tabUpcoming = document.getElementById('tab-upcoming');
        const tabPast = document.getElementById('tab-past');
        
        if (tab === 'upcoming') {
            tabUpcoming.style.color = 'var(--t)';
            tabUpcoming.style.borderBottomColor = 'var(--t)';
            tabUpcoming.style.opacity = '1';
            
            tabPast.style.color = 'var(--gm)';
            tabPast.style.borderBottomColor = 'transparent';
            tabPast.style.opacity = '0.5';
        } else {
            tabPast.style.color = 'var(--t)';
            tabPast.style.borderBottomColor = 'var(--t)';
            tabPast.style.opacity = '1';
            
            tabUpcoming.style.color = 'var(--gm)';
            tabUpcoming.style.borderBottomColor = 'transparent';
            tabUpcoming.style.opacity = '0.5';
        }
    }
    </script>
@endsection
