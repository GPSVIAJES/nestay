@extends('layouts.app')

@section('content')
<div id="view-home">

    <!-- HERO SECTION -->
    <section class="hero" style="min-height: calc(100vh - 66px); display: grid; grid-template-columns: 1fr 1fr; overflow: hidden; background: var(--cr); position: relative; padding: 0;">
        <div class="hero-left" style="padding: 56px 48px 56px 56px; display: flex; flex-direction: column; justify-content: center; z-index: 5; position: relative;">
            
            <!-- Decorative background SVG lines -->
            <svg style="position:absolute;inset:0;width:100%;height:100%;pointer-events:none;z-index:0;opacity:.55" viewBox="0 0 700 700" preserveAspectRatio="xMidYMid slice">
                <path d="M-40 500 Q200 300 500 400" stroke="#E07A5F" stroke-width="0.7" fill="none" opacity=".18"/>
                <path d="M0 600 Q250 350 600 450" stroke="#81B29A" stroke-width="0.6" fill="none" opacity=".14"/>
                <circle cx="80" cy="120" r="2" fill="#E07A5F" opacity=".18"/>
                <circle cx="580" cy="160" r="2" fill="#81B29A" opacity=".16"/>
                <circle cx="660" cy="60" r="120" stroke="#E07A5F" stroke-width="0.8" fill="none" opacity=".07"/>
            </svg>

            <div class="slogan-tag"><span class="spark">✦</span> Tu nido en cada rincón del mundo</div>

            <h1 class="hero-h1" style="font-family:'Fraunces',serif; font-size:72px; line-height:.95; font-weight:900; letter-spacing:-2.5px; margin-bottom:14px; animation:fadeUp .6s cubic-bezier(.22,1,.36,1) .06s both;">
                Tu nido en cada<br>
                <span class="it" style="font-style:italic; color:var(--t); font-weight:400; font-size:76px; letter-spacing:-2px; display:block; line-height:.95;">rincón del mundo.</span>
            </h1>

            <blockquote class="hero-slogan" style="font-family:'Instrument Serif',serif; font-size:18px; font-weight:400; font-style:italic; color:var(--gm); border-left:3px solid var(--t); padding-left:18px; margin-bottom:28px; max-width:460px; animation:fadeUp .6s cubic-bezier(.22,1,.36,1) .14s both;">
                "No buscamos hoteles.<br>Encontramos tu <strong>próximo nido en el mundo.</strong>"
            </blockquote>

            <!-- MAIN SEARCH BOX (SBOX) -->
            <div class="sbox" id="main-search">
                <div class="stabs">
                    <button class="stab on">Hoteles</button>
                    <button class="stab">Apartamentos</button>
                    <button class="stab">Villas</button>
                </div>
                
                <form action="{{ route('search') }}" method="GET" onsubmit="showLoader()">
                    <div class="sfields">
                        <div class="sfield">
                            <label>Destino</label>
                            <input type="text" name="destination" id="dest" placeholder="¿A dónde quieres ir?" required autocomplete="off">
                            <div id="autocomplete-results" class="autocomplete-dropdown" style="display:none"></div>
                        </div>
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 9px;">
                            <div class="sfield">
                                <label>Llegada</label>
                                <input type="date" name="check_in" id="cin" required>
                            </div>
                            <div class="sfield">
                                <label>Salida</label>
                                <input type="date" name="check_out" id="cout" required>
                            </div>
                        </div>
                    </div>

                    <!-- GUEST DROPDOWN -->
                    <div style="display:flex; align-items:center; gap:10px; margin-top:14px; position:relative;">
                        <div class="guest-trigger" id="guest-trigger" onclick="SearchModule.toggleGuestPanel()">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <span id="guest-summary">2 adultos · 1 habitación</span>
                            <svg class="guest-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>

                        <input type="hidden" name="adults" id="adults-input" value="2">
                        <input type="hidden" name="rooms" id="rooms-input" value="1">

                        <button type="submit" class="scta">
                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="6.5" cy="6.5" r="4.5"/><line x1="10" y1="10" x2="14.5" y2="14.5"/></svg>
                            Buscar nido
                        </button>

                        <div class="guest-panel" id="guest-panel" style="top: 100%; position: absolute; width: 100%; left: 0;">
                            <div class="gp-row">
                                <div class="gp-info">
                                    <div class="gp-label">Adultos</div>
                                    <div class="gp-sub">13 años o más</div>
                                </div>
                                <div class="gp-ctrl">
                                    <button type="button" class="gp-btn" onclick="SearchModule.adjustGuest('adults', -1)">−</button>
                                    <span class="gp-num" id="gp-adults">2</span>
                                    <button type="button" class="gp-btn" onclick="SearchModule.adjustGuest('adults', 1)">+</button>
                                </div>
                            </div>
                            <div class="gp-row">
                                <div class="gp-info">
                                    <div class="gp-label">Habitaciones</div>
                                </div>
                                <div class="gp-ctrl">
                                    <button type="button" class="gp-btn" onclick="SearchModule.adjustGuest('rooms', -1)">−</button>
                                    <span class="gp-num" id="gp-rooms">1</span>
                                    <button type="button" class="gp-btn" onclick="SearchModule.adjustGuest('rooms', 1)">+</button>
                                </div>
                            </div>
                            <button type="button" class="gp-done" onclick="SearchModule.toggleGuestPanel()">Listo</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="hstats">
                <div class="hstat"><div class="n">2.4M+</div><div class="d">Alojamientos</div></div>
                <div class="hstat"><div class="n">190+</div><div class="d">Países</div></div>
                <div class="hstat"><div class="n">4.8★</div><div class="d">Rating</div></div>
            </div>
        </div>

        <!-- HERO RIGHT: MAP AND PINS -->
        <div class="hero-right" style="position:relative; overflow:hidden; background:var(--crd);">
            <div style="position:absolute; inset:0; background:linear-gradient(165deg,#EDE9CC 0%,#E8E4C4 55%,#E2DCBA 100%)">
                <div style="position:absolute; inset:0; background-image:radial-gradient(circle,rgba(47,47,47,.12) 1px,transparent 1px); background-size:22px 22px;"></div>
                
                <!-- Simplified World Map SVG -->
                <svg style="position:absolute;inset:0;width:100%;height:100%" viewBox="0 0 420 700" preserveAspectRatio="xMidYMid meet" fill="none">
                    <g fill="#C4B99A" opacity=".55">
                        <path d="M10 80 L30 60 L130 110 L88 185 L80 175 Z"/> <!-- North America dummy -->
                        <path d="M200 95 L240 115 L265 133 L200 108 Z"/> <!-- Europe dummy -->
                        <path d="M195 148 L258 175 L232 295 L193 172 Z"/> <!-- Africa dummy -->
                    </g>
                    <!-- Animated Routes -->
                    <path d="M404 97 Q360 70 302 135" stroke="#E07A5F" stroke-width="1.2" fill="none" stroke-dasharray="4 5" opacity=".55"/>
                    <circle r="3" fill="#E07A5F"><animateMotion dur="4s" repeatCount="indefinite" path="M404 97 Q360 70 302 135"/></circle>
                </svg>

                <!-- Floating Pins -->
                <div style="position:absolute; left:26%; top:21%; transform:translate(-50%,-100%); animation:pinBounce 4.2s ease-in-out infinite;">
                    <div style="width:24px;height:24px;background:var(--v);border-radius:50% 50% 50% 0;transform:rotate(-45deg);display:flex;align-items:center;justify-content:center">
                        <div style="width:8px;height:8px;background:#fff;border-radius:50%;transform:rotate(45deg)"></div>
                    </div>
                    <div style="position:absolute;top:-30px;left:50%;transform:translateX(-50%);background:var(--g);color:#fff;font-size:9px;font-weight:700;padding:3px 9px;border-radius:100px;white-space:nowrap">Nueva York</div>
                </div>

                <div style="position:absolute; right:10%; bottom:15%; background:var(--wh); padding:20px; border-radius:20px; box-shadow:var(--shl); text-align:center;">
                    <div style="font-family:'Fraunces',serif; font-size:20px; font-weight:700; color:var(--t);">190+ países</div>
                    <div style="font-size:12px; color:var(--gm);">Tu nido te espera</div>
                </div>
            </div>
        </div>
    </section>

    <!-- MARQUEE BAR -->
    <div class="mq-wrap">
        <div class="mq-track">
            <span class="mq-item"><span class="mq-dot"></span>Madrid</span>
            <span class="mq-item"><span class="mq-dot"></span>Barcelona</span>
            <span class="mq-item"><span class="mq-dot"></span>Roma</span>
            <span class="mq-item"><span class="mq-dot"></span>París</span>
            <span class="mq-item"><span class="mq-dot"></span>Londres</span>
            <span class="mq-item"><span class="mq-dot"></span>Tokio</span>
            <span class="mq-item"><span class="mq-dot"></span>Madrid</span>
            <span class="mq-item"><span class="mq-dot"></span>Barcelona</span>
            <span class="mq-item"><span class="mq-dot"></span>Roma</span>
            <span class="mq-item"><span class="mq-dot"></span>París</span>
            <span class="mq-item"><span class="mq-dot"></span>Londres</span>
            <span class="mq-item"><span class="mq-dot"></span>Tokio</span>
        </div>
    </div>

    <!-- HOW IT WORKS SECTION -->
    <section class="home-sec hiw" id="how">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:64px; align-items:center;">
            <div class="reveal">
                <div class="sec-tag">¿Cómo funciona Nestay?</div>
                <h2 class="sec-h2">Busca, reserva y vive.<br><em style="font-family:'Instrument Serif',serif; font-style:italic; color:var(--t)">Así de sencillo.</em></h2>
                
                <div class="hiw-steps" style="margin-top:28px">
                    <div class="step on">
                        <div class="snum">1</div>
                        <div class="stxt">
                            <div class="sh">Escribe tu destino</div>
                            <div class="sp">Nuestro motor busca en tiempo real entre 2.4M+ alojamientos.</div>
                        </div>
                    </div>
                    <div class="step">
                        <div class="snum">2</div>
                        <div class="stxt">
                            <div class="sh">Compara y filtra</div>
                            <div class="sp">Precio, valoración o servicios. Elige el nido perfecto.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="phone-wrap reveal d2">
                <div class="phone">
                    <div class="p-notch"><div class="p-notch-d"></div></div>
                    <div class="p-screen">
                        <div class="p-title">📍 Madrid</div>
                        <div class="p-result">
                            <div class="rcard-img" style="height:40px; font-size:16px;">🏨</div>
                            <div>
                                <div style="font-size:11px; font-weight:700;">Palacio Gran Vía</div>
                                <div style="font-size:10px; color:var(--t);">€149/noche</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- DESTINATIONS -->
    <section class="home-sec" id="destinations">
        <div class="reveal">
            <div class="sec-tag">Explora el mundo</div>
            <h2 class="sec-h2">Viaja a donde siempre<br>has soñado ir</h2>
        </div>
        <div class="dest-grid reveal d2">
            <div class="dcard">
                <div class="dbg">🌊</div>
                <div class="dov"></div>
                <div class="dcont"><div class="dcat">Escapada</div><div class="dname">Playa</div></div>
            </div>
            <div class="dcard">
                <div class="dbg">🏙️</div>
                <div class="dov"></div>
                <div class="dcont"><div class="dcat">Urbana</div><div class="dname">Ciudad</div></div>
            </div>
            <div class="dcard">
                <div class="dbg">🏔️</div>
                <div class="dov"></div>
                <div class="dcont"><div class="dcat">Relax</div><div class="dname">Montaña</div></div>
            </div>
        </div>
    </section>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Init Hero Autocomplete
        SearchModule.initAutocomplete('dest', 'autocomplete-results');

        // Reveal animations observer
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('vis');
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    });
</script>
@endsection
