@extends('layouts.app')

@section('content')
<div id="view-home">

    <!-- HERO MASTER MIX (WITH TRAVEL BACKGROUND) -->
    <section class="hero-mix">
        <div class="hero-bg"></div>
        
        <div class="hero-content">
            <h1 class="hero-h1">Tu nido en cada<br><span>rincón del mundo.</span></h1>
            
            <div class="hero-quote">
                "No buscamos hoteles.<br>Encontramos tu <strong>próximo nido en el mundo.</strong>"
            </div>

            <!-- TABBED SEARCH BOX -->
            <div class="sbox-mix">
                <div class="stabs-mix">
                    <button class="stab-mix on">Hoteles</button>
                    <button class="stab-mix">Apartamentos</button>
                    <button class="stab-mix">Casas rurales</button>
                    <button class="stab-mix">Villas</button>
                </div>
                
                <form action="{{ route('search') }}" method="GET" onsubmit="showLoader()">
                    <div class="sfields-mix">
                        <div class="sf-box">
                            <label class="sf-label">📍 Destino</label>
                            <input type="text" name="destination" id="dest" class="sf-val" placeholder="Ciudad, país o nombre..." required autocomplete="off">
                        </div>
                        <div class="sf-box">
                            <label class="sf-label">📅 Llegada</label>
                            <input type="date" name="check_in" id="cin" class="sf-val" required>
                        </div>
                        <div class="sf-box">
                            <label class="sf-label">📅 Salida</label>
                            <input type="date" name="check_out" id="cout" class="sf-val" required>
                        </div>
                        <div class="sf-box" style="cursor:pointer" onclick="SearchMix.toggleGuest()">
                            <label class="sf-label">👥 Huéspedes</label>
                            <div class="sf-val" id="guest-summary">2 adultos · 1 hab</div>
                            <input type="hidden" name="adults" id="adults-input" value="2">
                            <input type="hidden" name="rooms" id="rooms-input" value="1">
                        </div>
                    </div>

                    <div style="padding: 12px; display:flex; justify-content:flex-end">
                        <button type="submit" class="sbtn-mix">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                            Buscar nido
                        </button>
                    </div>

                    <!-- GUEST PANEL (UI FIXED) -->
                    <div class="guest-hub-panel" id="guest-hub-panel">
                        <div class="hub-row">
                            <div class="hub-info">
                                <h4>Adultos</h4>
                                <p>13 años o más</p>
                            </div>
                            <div class="hub-ctrl">
                                <button type="button" class="hub-btn" onclick="SearchMix.adjust('adults', -1)">−</button>
                                <span class="hub-num" id="hub-adults">2</span>
                                <button type="button" class="hub-btn" onclick="SearchMix.adjust('adults', 1)">+</button>
                            </div>
                        </div>
                        <div class="hub-row">
                            <div class="hub-info"><h4>Habitaciones</h4></div>
                            <div class="hub-ctrl">
                                <button type="button" class="hub-btn" onclick="SearchMix.adjust('rooms', -1)">−</button>
                                <span class="hub-num" id="hub-rooms">1</span>
                                <button type="button" class="hub-btn" onclick="SearchMix.adjust('rooms', 1)">+</button>
                            </div>
                        </div>
                        <button type="button" class="btn-primary" style="width:100%; margin-top:16px; border-radius:15px" onclick="SearchMix.toggleGuest()">Listo</button>
                    </div>
                </form>
            </div>
            
            <!-- STATS (IMAGE 1) -->
            <div style="display:flex; justify-content:center; gap:48px; margin-top:48px">
                <div style="text-align:center"><div style="font-family:'Fraunces',serif; font-size:36px; font-weight:800; color:#fff">2.4M+</div><div style="font-size:13px; color:#fff; opacity:0.8">Alojamientos</div></div>
                <div style="text-align:center"><div style="font-family:'Fraunces',serif; font-size:36px; font-weight:800; color:#fff">190+</div><div style="font-size:13px; color:#fff; opacity:0.8">Países</div></div>
                <div style="text-align:center"><div style="font-family:'Fraunces',serif; font-size:36px; font-weight:800; color:#fff">4.8★</div><div style="font-size:13px; color:#fff; opacity:0.8">Valoración</div></div>
                <div style="text-align:center"><div style="font-family:'Fraunces',serif; font-size:36px; font-weight:800; color:#fff">24/7</div><div style="font-size:13px; color:#fff; opacity:0.8">Soporte</div></div>
            </div>
        </div>
    </section>

    <!-- CITY MARQUEE ORANGE (IMAGE 2) -->
    <div class="mq-orange">
        <div class="mq-scroll">
            <span class="mq-city">MADRID</span>
            <span class="mq-city">BARCELONA</span>
            <span class="mq-city">PARÍS</span>
            <span class="mq-city">ROMA</span>
            <span class="mq-city">LONDRES</span>
            <span class="mq-city">TOKIO</span>
            <span class="mq-city">CDMX</span>
            <span class="mq-city">BERLÍN</span>
            <span class="mq-city">AMSTERDAM</span>
            <!-- Repeat -->
            <span class="mq-city">MADRID</span>
            <span class="mq-city">BARCELONA</span>
            <span class="mq-city">PARÍS</span>
            <span class="mq-city">ROMA</span>
        </div>
    </div>

    <!-- VALUE PROPOSITIONS BAR -->
    <div class="prop-bar">
        <div class="prop-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> Pago 100% seguro</div>
        <div class="prop-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Confirmación inmediata</div>
        <div class="prop-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Soporte 24/7</div>
        <div class="prop-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg> Mejor precio garantizado</div>
        <div class="prop-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg> 2.4M+ alojamientos</div>
    </div>

    <!-- FEATURED ACCOMODATIONS (IMAGE 2 REPLICA) -->
    <section style="padding: 100px 64px; background: var(--cr);">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:48px">
            <div>
                <span style="background:var(--tp); color:var(--t); padding:5px 12px; border-radius:100px; font-size:10px; font-weight:800; letter-spacing:1px">ALOJAMIENTOS DESTACADOS</span>
                <h2 style="font-family:'Fraunces',serif; font-size:48px; font-weight:800; color:var(--v); margin-top:12px">Los más reservados ahora</h2>
            </div>
            <a style="font-size:14px; font-weight:700; color:var(--gm); cursor:pointer">Ver todos →</a>
        </div>

        <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:24px">
            <!-- CARD 1 -->
            <div class="pcard-mix">
                <div class="p-img-box" style="background:#F2DED4">🏨</div>
                <div class="p-body">
                    <div class="p-loc">📍 Madrid, España</div>
                    <h4 class="p-name">Hotel Palacio Gran Vía</h4>
                    <div class="p-price">Desde <strong>€149</strong> / noche</div>
                    <div class="p-footer">
                        <span class="p-tag">Hotel 5★</span>
                        <span class="p-rate">⭐ 4.9</span>
                    </div>
                </div>
            </div>
            <!-- CARD 2 -->
            <div class="pcard-mix">
                <div class="p-img-box" style="background:#D4EBF2">🌊</div>
                <div class="p-body">
                    <div class="p-loc">📍 Barcelona</div>
                    <h4 class="p-name">Aptos. Mar Barceloneta</h4>
                    <div class="p-price">Desde <strong>€112</strong> / noche</div>
                    <div class="p-footer">
                        <span class="p-tag" style="background:#FFF1EF; color:#EE6C4D; border-color:#FFDED8">🔥 OFERTA</span>
                        <span class="p-rate">⭐ 4.7</span>
                    </div>
                </div>
            </div>
            <!-- CARD 3 -->
            <div class="pcard-mix">
                <div class="p-img-box" style="background:#F2E4D4">☀️</div>
                <div class="p-body">
                    <div class="p-loc">📍 Málaga</div>
                    <h4 class="p-name">Villa Mediterránea</h4>
                    <div class="p-price">Desde <strong>€195</strong> / noche</div>
                    <div class="p-footer">
                        <span class="p-tag">Villa · Piscina</span>
                        <span class="p-rate">⭐ 5.0</span>
                    </div>
                </div>
            </div>
            <!-- CARD 4 -->
            <div class="pcard-mix">
                <div class="p-img-box" style="background:#EBF2D4">🌿</div>
                <div class="p-body">
                    <div class="p-loc">📍 Sierra Nevada</div>
                    <h4 class="p-name">Casa Rural Sierra</h4>
                    <div class="p-price">Desde <strong>€78</strong> / noche</div>
                    <div class="p-footer">
                        <span class="p-tag" style="background:#FFF1EF; color:#EE6C4D; border-color:#FFDED8">🔥 OFERTA</span>
                        <span class="p-rate">⭐ 4.8</span>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- HOW IT WORKS (IMAGE 3) -->
    <section class="hiw-mix" id="how">
        <div class="reveal">
            <span class="hiw-tag">¿Cómo funciona Nestay?</span>
            <h2 class="hiw-h2">Busca, reserva y vive.<br><em style="font-family:'Instrument Serif',serif; font-style:italic; color:var(--t)">Así de sencillo.</em></h2>
            
            <div class="hiw-step on">
                <div class="h-num">1</div>
                <div class="h-body">
                    <div class="h-title">Escribe tu destino</div>
                    <p class="h-desc">Ciudad, país o región. Nuestro motor busca en tiempo real entre 2.4M+ alojamientos verificados en 190+ países.</p>
                </div>
            </div>
            <div class="hiw-step">
                <div class="h-num">2</div>
                <div class="h-body">
                    <div class="h-title">Compara y filtra</div>
                    <p class="h-desc">Precio, categoría, valoración o amenities. Ve fotos reales y reseñas verificadas antes de decidir.</p>
                </div>
            </div>
            <div class="hiw-step">
                <div class="h-num">3</div>
                <div class="h-body">
                    <div class="h-title">Reserva con garantía</div>
                    <p class="h-desc">Pago seguro, confirmación inmediata, bono en tu email. Sin comisiones ocultas. Tu nido, garantizado.</p>
                </div>
            </div>
        </div>

        <!-- PHONE MOCKUP (IMAGE 3) -->
        <div class="reveal d2" style="position:relative">
            <div style="background:var(--wh); border-radius:40px; border:8px solid #333; height:500px; width:260px; margin:0 auto; overflow:hidden; box-shadow:var(--shl); position:relative">
                <div style="background:#000; height:20px; width:120px; border-radius:0 0 12px 12px; margin:0 auto"></div>
                <!-- Mockup Content -->
                <div style="padding:20px">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px">
                        <span style="font-size:18px">🌍</span>
                        <div style="font-size:11px; font-weight:700">Destino</div>
                    </div>
                    <div style="background:var(--tp); border:1px solid var(--tl); border-radius:12px; padding:12px; margin-bottom:12px">
                        <div style="font-size:12px; font-weight:700">🔍 Roma, Italia</div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px">
                        <div style="background:var(--tp); border-radius:10px; padding:10px; font-size:10px">LLEGADA<br><strong>15 Ago</strong></div>
                        <div style="background:var(--tp); border-radius:10px; padding:10px; font-size:10px">SALIDA<br><strong>18 Ago</strong></div>
                    </div>
                    <div style="margin-top:12px; background:var(--tp); border-radius:10px; padding:10px; font-size:10px">2 huéspedes · 1 hab.</div>
                    <button style="margin-top:20px; width:100%; background:var(--t); color:#fff; border:none; border-radius:10px; padding:12px; font-size:12px; font-weight:700">Buscar nidos →</button>
                </div>
            </div>
        </div>
    </section>

    <!-- EXPLORE THE WORLD (IMAGE 4) -->
    <section class="dest-mix" id="destinations">
        <h2 class="dest-h2">Viaja a donde siempre<br>has soñado ir</h2>
        
        <div class="dgrid-mix">
            <div class="dcard-mix c1">
                <div class="d-icon">🌊</div>
                <div class="d-tag">Escapada</div>
                <div class="d-name">Playa</div>
                <div class="d-meta">4.200+ alojamientos</div>
            </div>
            <div class="dcard-mix c2">
                <div class="d-icon">🏙️</div>
                <div class="d-tag">Urbana</div>
                <div class="d-name">Ciudad</div>
                <div class="d-meta">12.800+ alojamientos</div>
            </div>
            <div class="dcard-mix c3">
                <div class="d-icon">🏔️</div>
                <div class="d-tag">Aventura</div>
                <div class="d-name">Montaña</div>
                <div class="d-meta">2.100+ alojamientos</div>
            </div>
            <div class="dcard-mix c4">
                <div class="d-icon">🌾</div>
                <div class="d-tag">Relax</div>
                <div class="d-name">Rural</div>
                <div class="d-meta">1.400+ alojamientos</div>
            </div>
        </div>
    </section>

</div>

<script>
    const SearchMix = {
        toggleGuest() {
            document.getElementById('guest-hub-panel').classList.toggle('active');
        },
        adjust(type, val) {
            const input = document.getElementById(`${type}-input`);
            const display = document.getElementById(`hub-${type}`);
            let curr = parseInt(input.value);
            curr = Math.max(1, curr + val);
            input.value = curr;
            display.innerText = curr;
            this.updateSummary();
        },
        updateSummary() {
            const a = document.getElementById('adults-input').value;
            const r = document.getElementById('rooms-input').value;
            document.getElementById('guest-summary').innerText = `${a} adultos · ${r} ${r==1?'hab':'habs'}`;
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        const obs = new IntersectionObserver((es) => {
            es.forEach(e => { if(e.isIntersecting) e.target.classList.add('vis'); });
        }, {threshold: 0.1});
        document.querySelectorAll('.reveal').forEach(x => obs.observe(x));
    });
</script>
@endsection
