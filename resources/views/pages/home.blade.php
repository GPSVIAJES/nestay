@extends('layouts.app')

@section('content')
    <div id="view-home">

        <!-- HERO MASTER MIX (WITH TRAVEL BACKGROUND) -->
        <section class="hero-mix">
            <div class="hero-bg"></div>

            <div class="hero-content">
                <h1 class="hero-h1">Tu nido en cada<br><span>rincón del mundo.</span></h1>

                <div class="hero-quote">
                    "Encuentra el hospedaje que te <strong>haga sentir en casa.</strong>"
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
                                <label class="sf-label">
                                    <svg class="sf-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                                    Destino
                                </label>
                                <input type="text" name="destination" id="dest" class="sf-val"
                                    placeholder="¿Dónde vas?" required autocomplete="off">
                            </div>
                            <div class="sf-box">
                                <label class="sf-label">
                                    <svg class="sf-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    Fecha de Inicio
                                </label>
                                <input type="text" onfocus="(this.type='date')" onblur="(this.type=this.value?'date':'text')" name="check_in" id="cin" class="sf-val" placeholder="Añade una fecha" required>
                            </div>
                            <div class="sf-box">
                                <label class="sf-label">
                                    <svg class="sf-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    Fecha Final
                                </label>
                                <input type="text" onfocus="(this.type='date')" onblur="(this.type=this.value?'date':'text')" name="check_out" id="cout" class="sf-val" placeholder="Añade una fecha" required>
                            </div>
                            <!-- Huéspedes Dropdown -->
                            <div class="sf-box sf-box-guest" style="position: relative;">
                                <div style="cursor:pointer" onclick="SearchMix.toggleGuest()">
                                    <label class="sf-label">
                                        <svg class="sf-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        Huéspedes
                                    </label>
                                    <div class="sf-val empty-styled" id="guest-summary">Añadir huéspedes</div>
                                    <input type="hidden" name="adults" id="adults-input" value="2">
                                    <input type="hidden" name="children" id="children-input" value="0">
                                    <input type="hidden" name="rooms" id="rooms-input" value="1">
                                </div>

                                <!-- GUEST PANEL — Room-based layout (Nested) -->
                                <div class="guest-hub-panel" id="guest-hub-panel">
                                    <div id="rooms-container">
                                        <!-- Room 1 (default) -->
                                        <div class="room-block" data-room="1">
                                            <div class="room-header">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--t)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                                                <span>Habitación 1</span>
                                            </div>

                                            <div class="hub-row">
                                                <div class="hub-info">
                                                    <h4>Adultos</h4>
                                                </div>
                                                <div class="hub-ctrl">
                                                    <button type="button" class="hub-btn" onclick="SearchMix.adjustRoom(1,'adults',-1)">−</button>
                                                    <span class="hub-num" id="room-1-adults">2</span>
                                                    <button type="button" class="hub-btn" onclick="SearchMix.adjustRoom(1,'adults',1)">+</button>
                                                </div>
                                            </div>

                                            <div class="hub-children-list" id="room-1-children"></div>

                                            <button type="button" class="add-child-btn" onclick="SearchMix.addChild(1)">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                                Añadir un niño
                                            </button>
                                        </div>
                                    </div>

                                    <button type="button" class="add-room-btn" onclick="SearchMix.addRoom()">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        Agregar habitación
                                    </button>

                                    <button type="button" class="guest-done-btn" onclick="SearchMix.toggleGuest()">Listo</button>
                                </div>
                            </div>

                            <!-- Buscar nido inline -->
                            <button type="submit" class="sbtn-mix">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="M21 21l-4.35-4.35" />
                                </svg>
                                Buscar nido
                            </button>
                        </div>
                    </form>
                </div>

                <!-- STATS (IMAGE 1) -->
                <div style="display:flex; justify-content:center; gap:48px; margin-top:80px">
                    <div style="text-align:center">
                        <div style="font-family:'DM Sans',serif; font-size:36px; font-weight:800; color:#fff">2.4M+</div>
                        <div style="font-size:13px; color:#fff; opacity:0.8">Alojamientos</div>
                    </div>
                    <div style="text-align:center">
                        <div style="font-family:'DM Sans',serif; font-size:36px; font-weight:800; color:#fff">190+</div>
                        <div style="font-size:13px; color:#fff; opacity:0.8">Países</div>
                    </div>
                    <div style="text-align:center">
                        <div style="font-family:'DM Sans',serif; font-size:36px; font-weight:800; color:#fff">4.8★</div>
                        <div style="font-size:13px; color:#fff; opacity:0.8">Valoración</div>
                    </div>
                    <div style="text-align:center">
                        <div style="font-family:'DM Sans',serif; font-size:36px; font-weight:800; color:#fff">24/7</div>
                        <div style="font-size:13px; color:#fff; opacity:0.8">Soporte</div>
                    </div>
                </div>
            </div>

            <!-- CITY MARQUEE NOW INSIDE HERO (IMAGE 2) -->
            <div class="mq-orange">
                <div class="mq-scroll">
                    <!-- BLOCK 1 -->
                    <span class="mq-city">MADRID</span>
                    <span class="mq-city">PARÍS</span>
                    <span class="mq-city">ROMA</span>
                    <span class="mq-city">LONDRES</span>
                    <span class="mq-city">TOKIO</span>
                    <span class="mq-city">CIUDAD DE MÉXICO</span>
                    <span class="mq-city">BERLÍN</span>
                    <span class="mq-city">NUEVA YORK</span>
                    <span class="mq-city">BUENOS AIRES</span>
                    <span class="mq-city">BOGOTÁ</span>
                    <span class="mq-city">LIMA</span>
                    <span class="mq-city">SANTIAGO</span>
                    <span class="mq-city">SÃO PAULO</span>
                    <span class="mq-city">RÍO DE JANEIRO</span>
                    <span class="mq-city">LISBOA</span>
                    <span class="mq-city">AMSTERDAM</span>
                    <span class="mq-city">VIENA</span>
                    <span class="mq-city">PRAGA</span>
                    <span class="mq-city">BUDAPEST</span>
                    <span class="mq-city">ESTAMBUL</span>
                    <span class="mq-city">DUBÁI</span>
                    <span class="mq-city">BANGKOK</span>
                    <span class="mq-city">SINGAPUR</span>
                    <span class="mq-city">SEÚL</span>
                    <span class="mq-city">PEKÍN</span>
                    <span class="mq-city">SÍDNEY</span>
                    <span class="mq-city">LOS ÁNGELES</span>
                    <span class="mq-city">MIAMI</span>
                    <span class="mq-city">TORONTO</span>
                    <span class="mq-city">EL CAIRO</span>
                    <!-- BLOCK 2 (for seamless loop) -->
                    <span class="mq-city">MADRID</span>
                    <span class="mq-city">PARÍS</span>
                    <span class="mq-city">ROMA</span>
                    <span class="mq-city">LONDRES</span>
                    <span class="mq-city">TOKIO</span>
                    <span class="mq-city">CIUDAD DE MÉXICO</span>
                    <span class="mq-city">BERLÍN</span>
                    <span class="mq-city">NUEVA YORK</span>
                    <span class="mq-city">BUENOS AIRES</span>
                    <span class="mq-city">BOGOTÁ</span>
                    <span class="mq-city">LIMA</span>
                    <span class="mq-city">SANTIAGO</span>
                    <span class="mq-city">SÃO PAULO</span>
                    <span class="mq-city">RÍO DE JANEIRO</span>
                    <span class="mq-city">LISBOA</span>
                    <span class="mq-city">AMSTERDAM</span>
                    <span class="mq-city">VIENA</span>
                    <span class="mq-city">PRAGA</span>
                    <span class="mq-city">BUDAPEST</span>
                    <span class="mq-city">ESTAMBUL</span>
                    <span class="mq-city">DUBÁI</span>
                    <span class="mq-city">BANGKOK</span>
                    <span class="mq-city">SINGAPUR</span>
                    <span class="mq-city">SEÚL</span>
                    <span class="mq-city">PEKÍN</span>
                    <span class="mq-city">SÍDNEY</span>
                    <span class="mq-city">LOS ÁNGELES</span>
                    <span class="mq-city">MIAMI</span>
                    <span class="mq-city">TORONTO</span>
                    <span class="mq-city">EL CAIRO</span>
                </div>
            </div>
        </section>


        <!-- VALUE PROPOSITIONS BAR -->
        <div class="prop-bar">
            <div class="prop-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg> Pago 100% seguro</div>
            <div class="prop-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12" />
                </svg> Confirmación inmediata</div>
            <div class="prop-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg> Soporte 24/7</div>
            <div class="prop-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                </svg> Mejor precio garantizado</div>
            <div class="prop-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg> 2.4M+ alojamientos</div>
        </div>


        <!-- HOW IT WORKS (IMAGE 3) -->
        <section class="hiw-mix" id="how">
            <div class="reveal">
                <span class="hiw-tag">¿Cómo funciona Nestay?</span>
                <h2 class="hiw-h2">Busca, reserva y vive.<br><em
                        style="font-family:'Instrument Serif',serif; font-style:italic; color:var(--t)">Así de
                        sencillo.</em></h2>

                <div class="hiw-step on">
                    <div class="h-num">1</div>
                    <div class="h-body">
                        <div class="h-title">Dinos a dónde quieres ir</div>
                        <p class="h-desc">Busca tu alojamiento en cualquier ciudad. Te conectamos con <strong>2.4 millones
                                de alojamientos verificados</strong> en más de 190 países que esperan por ti.</p>
                    </div>
                </div>
                <div class="hiw-step">
                    <div class="h-num">2</div>
                    <div class="h-body">
                        <div class="h-title">Compara como un experto</div>
                        <p class="h-desc">Compara <strong>precios, categorías, valoraciones reales y servicios</strong>.
                            Filtra por lo que realmente te importa y elige con total confianza el hospedaje que más se
                            ajuste a tu viaje.</p>
                    </div>
                </div>
                <div class="hiw-step">
                    <div class="h-num">3</div>
                    <div class="h-body">
                        <div class="h-title">Reserva en un clic, sin sorpresas</div>
                        <p class="h-desc">Pago 100% seguro, confirmación al instante y tu voucher directo en el email.
                            <strong>Un proceso de reserva sencillo, rápido y ágil</strong> para encontrar tu nido perfecto.
                        </p>
                    </div>
                </div>
            </div>

            <!-- PHONE MOCKUP — PREMIUM DYNAMIC -->
            <div class="reveal d2 mock-world-wrap">

                <!-- World map SVG silhouette (realistic) -->
                <svg class="mock-world-map" viewBox="0 0 800 500" fill="none" xmlns="http://www.w3.org/2000/svg">

                    <!-- North America -->
                    <path
                        d="M60 80 L100 60 L140 55 L170 65 L200 58 L230 70 L240 60 L260 68 L255 80 L235 90 L220 100 L230 115 L225 130 L210 140 L205 155 L215 165 L220 180 L210 195 L195 200 L185 210 L170 215 L155 205 L140 210 L130 225 L125 240 L115 250 L100 245 L90 230 L80 215 L70 200 L60 185 L50 170 L45 150 L50 130 L55 110 L60 95 Z"
                        fill="currentColor" opacity="0.07" />
                    <!-- Greenland -->
                    <path d="M260 40 L290 35 L310 45 L305 60 L290 65 L270 60 L260 50 Z" fill="currentColor"
                        opacity="0.06" />

                    <!-- South America -->
                    <path
                        d="M165 270 L175 260 L190 258 L205 265 L215 275 L220 290 L225 310 L218 330 L210 350 L200 370 L190 385 L180 395 L170 400 L165 410 L160 420 L150 425 L145 415 L148 400 L150 380 L145 360 L140 340 L138 320 L142 300 L150 285 L158 275 Z"
                        fill="currentColor" opacity="0.07" />

                    <!-- Europe -->
                    <path
                        d="M365 75 L375 68 L390 72 L405 65 L420 70 L435 75 L440 85 L430 95 L425 105 L430 115 L420 125 L410 130 L400 140 L390 145 L380 140 L370 145 L365 135 L355 130 L350 120 L355 110 L360 100 L358 90 Z"
                        fill="currentColor" opacity="0.08" />
                    <!-- UK -->
                    <path d="M350 80 L358 75 L362 82 L358 90 L352 88 Z" fill="currentColor" opacity="0.07" />
                    <!-- Scandinavia -->
                    <path d="M395 45 L405 40 L415 48 L420 60 L415 70 L408 65 L400 58 L395 50 Z" fill="currentColor"
                        opacity="0.06" />

                    <!-- Africa -->
                    <path
                        d="M370 155 L385 150 L400 155 L415 160 L430 165 L440 175 L445 190 L448 210 L445 230 L440 250 L432 270 L425 290 L420 310 L415 330 L405 345 L395 350 L388 340 L385 320 L380 300 L375 280 L370 260 L365 240 L360 220 L358 200 L360 180 L365 165 Z"
                        fill="currentColor" opacity="0.07" />

                    <!-- Asia -->
                    <path
                        d="M440 55 L460 50 L480 55 L510 48 L540 52 L570 55 L600 50 L630 55 L660 60 L680 70 L700 80 L710 95 L705 110 L695 120 L700 135 L690 145 L675 150 L660 145 L645 150 L630 155 L615 160 L600 155 L580 160 L560 155 L540 160 L520 155 L500 160 L485 155 L470 150 L455 140 L445 125 L440 110 L435 95 L440 80 L445 65 Z"
                        fill="currentColor" opacity="0.07" />
                    <!-- India -->
                    <path
                        d="M530 170 L545 165 L555 175 L560 190 L555 210 L545 225 L535 235 L525 225 L520 210 L518 195 L522 180 Z"
                        fill="currentColor" opacity="0.06" />
                    <!-- Southeast Asia -->
                    <path d="M600 170 L620 165 L635 175 L640 190 L630 200 L620 195 L610 200 L605 190 L600 180 Z"
                        fill="currentColor" opacity="0.06" />
                    <!-- Japan -->
                    <path d="M700 90 L708 85 L715 95 L712 110 L705 115 L700 105 Z" fill="currentColor" opacity="0.07" />

                    <!-- Australia -->
                    <path
                        d="M620 300 L650 290 L680 295 L710 300 L730 310 L735 325 L730 340 L720 350 L700 355 L680 350 L660 345 L640 340 L625 330 L618 315 Z"
                        fill="currentColor" opacity="0.06" />
                    <!-- Indonesia / Bali region -->
                    <path d="M610 260 L625 255 L640 260 L650 268 L645 275 L630 278 L615 275 L610 268 Z" fill="currentColor"
                        opacity="0.05" />

                    <!-- Dotted routes: NY → París → Tokio, París → Bali -->
                    <path d="M200 140 C260 110,310 90,380 125" stroke="var(--t)" stroke-width="1.5" stroke-dasharray="6 4"
                        opacity="0.3" class="mock-route r1" />
                    <path d="M395 125 C480 100,580 80,705 100" stroke="var(--t)" stroke-width="1.5" stroke-dasharray="6 4"
                        opacity="0.25" class="mock-route r2" />
                    <path d="M395 130 C450 180,540 230,635 265" stroke="var(--g)" stroke-width="1.5" stroke-dasharray="6 4"
                        opacity="0.2" class="mock-route r3" />

                </svg>

                <!-- Floating city pins (geographically positioned) -->
                <!-- New York: East coast North America -->
                <div class="mock-pin" style="top:26%; left:24%">
                    <div class="mock-pin-dot green"></div>
                    <div class="mock-pin-label">Nueva York</div>
                    <div class="mock-pin-price">$145/noche</div>
                </div>

                <!-- París: Western Europe -->
                <div class="mock-pin" style="top:22%; left:46%">
                    <div class="mock-pin-dot orange pulse-slow"></div>
                    <div class="mock-pin-label">París</div>
                    <div class="mock-pin-price">€132/noche</div>
                </div>

                <!-- Roma: just a dot near Italy -->
                <div class="mock-pin" style="top:28%; left:50%">
                    <div class="mock-pin-dot orange"></div>
                </div>

                <!-- Tokio: Japan -->
                <div class="mock-pin" style="top:18%; right:10%">
                    <div class="mock-pin-dot orange"></div>
                    <div class="mock-pin-label">Tokio</div>
                    <div class="mock-pin-price orange">€189/noche</div>
                </div>

                <!-- Bali: Indonesia -->
                <div class="mock-pin" style="bottom:22%; right:17%">
                    <div class="mock-pin-dot green pulse-slow"></div>
                    <div class="mock-pin-label">Bali</div>
                    <div class="mock-pin-price green">€78/noche</div>
                </div>

                <!-- Extra dot: Africa -->
                <div class="mock-pin" style="top:48%; left:52%">
                    <div class="mock-pin-dot green pulse-slow"></div>
                </div>

                <!-- Buenos Aires: South America east coast -->
                <div class="mock-pin" style="bottom:18%; left:22%">
                    <div class="mock-pin-dot green pulse-slow"></div>
                    <div class="mock-pin-label">Buenos Aires</div>
                    <div class="mock-pin-price green">€95/noche</div>
                </div>

                <!-- Phone -->
                <div class="mock-phone">
                    <div class="mock-notch"></div>
                    <div class="mock-screens" id="mockScreens">

                        <!-- SCREEN 1: SEARCH -->
                        <div class="mock-screen active" data-screen="0">
                            <div class="mock-brand-bar">
                                <svg width="18" height="18" viewBox="0 0 38 38" fill="none">
                                    <path
                                        d="M19 3.5C14 3.5 6.5 9.5 6.5 19.5L6.5 32C6.5 33.4 7.6 34.5 9 34.5L29 34.5C30.4 34.5 31.5 33.4 31.5 32L31.5 19.5C31.5 9.5 24 3.5 19 3.5Z"
                                        fill="var(--t)" />
                                    <circle cx="19" cy="24" r="5.8" fill="white" />
                                </svg>
                                <span class="mock-brand-name">Nestay</span>
                            </div>

                            <div class="mock-search-hero">
                                <div class="mock-search-hero-bg"></div>
                                <div class="mock-search-hero-text">
                                    <div style="font-size:16px; font-weight:800; color:#fff; line-height:1.1">Encuentra tu
                                        nido</div>
                                    <div style="font-size:10px; color:rgba(255,255,255,0.7); margin-top:4px">2.4M+
                                        alojamientos · 190+ países</div>
                                </div>
                            </div>

                            <div class="mock-field">
                                <div class="mock-field-icon">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--t)"
                                        stroke-width="2.5">
                                        <circle cx="11" cy="11" r="8" />
                                        <path d="M21 21l-4.35-4.35" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="mock-field-label">DESTINO</div>
                                    <div class="mock-field-val">Roma, Italia</div>
                                </div>
                            </div>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px">
                                <div class="mock-field-sm">
                                    <div class="mock-field-label">LLEGADA</div>
                                    <div class="mock-field-val">15 Ago</div>
                                </div>
                                <div class="mock-field-sm">
                                    <div class="mock-field-label">SALIDA</div>
                                    <div class="mock-field-val">18 Ago</div>
                                </div>
                            </div>
                            <div class="mock-field-sm">
                                <div class="mock-field-label">HUÉSPEDES</div>
                                <div class="mock-field-val">2 adultos · 1 habitación</div>
                            </div>
                            <button class="mock-btn">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff"
                                    stroke-width="2.5">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="M21 21l-4.35-4.35" />
                                </svg>
                                Buscar nidos
                            </button>
                        </div>

                        <!-- SCREEN 2: RESULTS / COMPARE -->
                        <div class="mock-screen" data-screen="1">
                            <div class="mock-results-header">
                                <div>
                                    <div style="font-size:14px; font-weight:800; color:var(--v)">Roma</div>
                                    <div style="font-size:9px; color:var(--gm)">15-18 Ago · 2 adultos</div>
                                </div>
                                <span class="mock-results-badge">324</span>
                            </div>

                            <!-- Filter chips -->
                            <div class="mock-filters">
                                <span class="mock-chip on">Todo</span>
                                <span class="mock-chip">Hoteles</span>
                                <span class="mock-chip">Aptos</span>
                                <span class="mock-chip">5★</span>
                            </div>

                            <!-- Hotel Card 1 — Featured -->
                            <div class="mock-hotel-lg">
                                <div class="mock-hotel-lg-img"
                                    style="background-image:url('https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&q=80&w=400'); background-size:cover; background-position:center">
                                    <span class="mock-hotel-badge">TOP</span>
                                </div>
                                <div class="mock-hotel-lg-body">
                                    <div class="mock-hotel-name">Hotel Palazzo Roma</div>
                                    <div class="mock-hotel-meta">⭐ 4.9 · Hotel 5★ · Desayuno incl.</div>
                                    <div
                                        style="display:flex; justify-content:space-between; align-items:center; margin-top:6px">
                                        <div class="mock-hotel-price">€149<span>/noche</span></div>
                                        <div class="mock-hotel-save">Ahorra 18%</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hotel Card 2 -->
                            <div class="mock-hotel">
                                <div class="mock-hotel-img"
                                    style="background-image:url('https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&q=80&w=200'); background-size:cover; background-position:center">
                                </div>
                                <div class="mock-hotel-info">
                                    <div class="mock-hotel-name">Villa Trastevere</div>
                                    <div class="mock-hotel-meta">⭐ 4.7 · Apartamento</div>
                                    <div class="mock-hotel-price">€98<span>/noche</span></div>
                                </div>
                            </div>

                            <!-- Hotel Card 3 -->
                            <div class="mock-hotel">
                                <div class="mock-hotel-img"
                                    style="background-image:url('https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&q=80&w=200'); background-size:cover; background-position:center">
                                </div>
                                <div class="mock-hotel-info">
                                    <div class="mock-hotel-name">Boutique Navona</div>
                                    <div class="mock-hotel-meta">⭐ 5.0 · Boutique</div>
                                    <div class="mock-hotel-price">€175<span>/noche</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- SCREEN 3: BOOKING CONFIRMED -->
                        <div class="mock-screen" data-screen="2">
                            <div class="mock-confirm-top">
                                <div class="mock-check-circle">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fff"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </div>
                                <div class="mock-confirm-title">¡Reserva confirmada!</div>
                                <div class="mock-confirm-sub">Tu voucher ha sido enviado a tu email</div>
                            </div>

                            <div class="mock-confirm-card">
                                <div class="mock-confirm-card-header">
                                    <div class="mock-confirm-hotel-thumb"
                                        style="background-image:url('https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&q=80&w=200'); background-size:cover; background-position:center">
                                    </div>
                                    <div>
                                        <div style="font-size:12px; font-weight:700; color:var(--v)">Hotel Palazzo Roma
                                        </div>
                                        <div style="font-size:9px; color:var(--gm)">Roma, Italia</div>
                                    </div>
                                </div>
                                <div class="mock-confirm-details">
                                    <div class="mock-confirm-row">
                                        <span>Check-in</span><strong>15 Ago 2025</strong>
                                    </div>
                                    <div class="mock-confirm-row">
                                        <span>Check-out</span><strong>18 Ago 2025</strong>
                                    </div>
                                    <div class="mock-confirm-row">
                                        <span>Huéspedes</span><strong>2 adultos</strong>
                                    </div>
                                    <div class="mock-confirm-row total">
                                        <span>Total (3 noches)</span><strong>€447</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="mock-tag-row">
                                <span class="mock-tag">✓ Pago seguro</span>
                                <span class="mock-tag">✓ Sin comisiones</span>
                                <span class="mock-tag">✓ Cancel. gratuita</span>
                            </div>
                        </div>
                    </div>

                    <!-- Screen dots -->
                    <div class="mock-dots">
                        <span class="mock-dot active"></span>
                        <span class="mock-dot"></span>
                        <span class="mock-dot"></span>
                    </div>
                </div>
            </div>
        </section>


        <!-- EXPLORE THE WORLD (IMAGE 4) -->
        <section class="dest-mix" id="destinations">
            <span class="hiw-tag">Explora el mundo</span>
            <h2 class="dest-h2" style="margin-top:12px">Viaja a donde siempre<br><em
                    style="font-family:'Instrument Serif',serif; font-style:italic; color:var(--t)">has soñado ir.</em></h2>

            <div class="dgrid-mix">
                <div class="dcard-mix c1">
                    <div class="d-tag">Escapada</div>
                    <div class="d-name">Playa</div>
                    <div class="d-meta">4.200+ alojamientos</div>
                </div>
                <div class="dcard-mix c2">
                    <div class="d-tag">Urbana</div>
                    <div class="d-name">Ciudad</div>
                    <div class="d-meta">12.800+ alojamientos</div>
                </div>
                <div class="dcard-mix c3">
                    <div class="d-tag">Aventura</div>
                    <div class="d-name">Montaña</div>
                    <div class="d-meta">2.100+ alojamientos</div>
                </div>
                <div class="dcard-mix c4">
                    <div class="d-tag">Relax</div>
                    <div class="d-name">Rural</div>
                    <div class="d-meta">1.400+ alojamientos</div>
                </div>
            </div>

        </section>


        <!-- FEATURED ACCOMODATIONS (IMAGE 2 REPLICA) -->
        <section style="padding: 100px 64px; background: var(--cr);">
            <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:48px">
                <div>
                    <span class="hiw-tag">ALOJAMIENTOS DESTACADOS</span>
                    <h2 class="hiw-h2">Los más reservados<br><em
                            style="font-family:'Instrument Serif',serif; font-style:italic; color:var(--t)">ahora.</em></h2>
                </div>
                <a style="font-size:14px; font-weight:700; color:var(--gm); cursor:pointer">Ver todos →</a>
            </div>

            <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:24px">
                <!-- CARD 1 -->
                <div class="pcard-mix">
                    <div class="p-img-box"
                        style="background-image: url('https://images.unsplash.com/photo-1541971875076-8f970d573be6?auto=format&fit=crop&q=80&w=600')">
                    </div>
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
                    <div class="p-img-box"
                        style="background-image: url('https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&q=80&w=600')">
                    </div>
                    <div class="p-body">
                        <div class="p-loc">📍 Barcelona</div>
                        <h4 class="p-name">Aptos. Mar Barceloneta</h4>
                        <div class="p-price">Desde <strong>€112</strong> / noche</div>
                        <div class="p-footer">
                            <span class="p-tag" style="background:#FFF1EF; color:#EE6C4D; border-color:#FFDED8">🔥
                                OFERTA</span>
                            <span class="p-rate">⭐ 4.7</span>
                        </div>
                    </div>
                </div>
                <!-- CARD 3 -->
                <div class="pcard-mix">
                    <div class="p-img-box"
                        style="background-image: url('https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&q=80&w=600')">
                    </div>
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
                    <div class="p-img-box"
                        style="background-image: url('/images/mountain_cabin.jpg')">
                    </div>
                    <div class="p-body">
                        <div class="p-loc">📍 Sierra Nevada</div>
                        <h4 class="p-name">Casa Rural Sierra</h4>
                        <div class="p-price">Desde <strong>€78</strong> / noche</div>
                        <div class="p-footer">
                            <span class="p-tag" style="background:#FFF1EF; color:#EE6C4D; border-color:#FFDED8">🔥
                                OFERTA</span>
                            <span class="p-rate">⭐ 4.8</span>
                        </div>
                    </div>
                </div>
            </div>

        </section>

    </div>

    <script>
        const SearchMix = {
            rooms: [{ adults: 2, children: [] }],
            roomCounter: 1,

            toggleGuest() {
                document.getElementById('guest-hub-panel').classList.toggle('active');
            },

            adjustRoom(roomIdx, type, val) {
                const room = this.rooms[roomIdx - 1];
                if (!room) return;
                if (type === 'adults') {
                    room.adults = Math.max(1, Math.min(6, room.adults + val));
                    document.getElementById(`room-${roomIdx}-adults`).innerText = room.adults;
                }
                this.syncInputs();
            },

            addChild(roomIdx) {
                const room = this.rooms[roomIdx - 1];
                if (!room || room.children.length >= 4) return;
                room.children.push(8);
                this.renderChildren(roomIdx);
                this.syncInputs();
            },

            removeChild(roomIdx, childIdx) {
                const room = this.rooms[roomIdx - 1];
                if (!room) return;
                room.children.splice(childIdx, 1);
                this.renderChildren(roomIdx);
                this.syncInputs();
            },

            setChildAge(roomIdx, childIdx, age) {
                const room = this.rooms[roomIdx - 1];
                if (!room) return;
                room.children[childIdx] = parseInt(age);
                this.syncInputs();
            },

            renderChildren(roomIdx) {
                const room = this.rooms[roomIdx - 1];
                const container = document.getElementById(`room-${roomIdx}-children`);
                if (!container || !room) return;

                container.innerHTML = room.children.map((age, i) => {
                    const options = Array.from({length: 18}, (_, a) =>
                        `<option value="${a}" ${a === age ? 'selected' : ''}>${a} años</option>`
                    ).join('');
                    return `
                        <div class="child-row">
                            <label>Niño ${i + 1}</label>
                            <select class="child-age-select" onchange="SearchMix.setChildAge(${roomIdx},${i},this.value)">
                                ${options}
                            </select>
                            <button type="button" class="child-remove" onclick="SearchMix.removeChild(${roomIdx},${i})">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                    `;
                }).join('');
            },

            addRoom() {
                if (this.rooms.length >= 4) return;
                this.roomCounter++;
                this.rooms.push({ adults: 2, children: [] });
                const idx = this.rooms.length;
                const container = document.getElementById('rooms-container');
                const block = document.createElement('div');
                block.className = 'room-block';
                block.dataset.room = idx;
                block.innerHTML = `
                    <div class="room-header">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--t)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                        <span>Habitación ${idx}</span>
                        <button type="button" class="remove-room" onclick="SearchMix.removeRoom(${idx})">Eliminar</button>
                    </div>
                    <div class="hub-row">
                        <div class="hub-info"><h4>Adultos</h4></div>
                        <div class="hub-ctrl">
                            <button type="button" class="hub-btn" onclick="SearchMix.adjustRoom(${idx},'adults',-1)">−</button>
                            <span class="hub-num" id="room-${idx}-adults">2</span>
                            <button type="button" class="hub-btn" onclick="SearchMix.adjustRoom(${idx},'adults',1)">+</button>
                        </div>
                    </div>
                    <div class="hub-children-list" id="room-${idx}-children"></div>
                    <button type="button" class="add-child-btn" onclick="SearchMix.addChild(${idx})">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Añadir un niño
                    </button>
                `;
                container.appendChild(block);
                this.syncInputs();
            },

            removeRoom(roomIdx) {
                if (this.rooms.length <= 1) return;
                this.rooms.splice(roomIdx - 1, 1);
                this.rebuildAllRooms();
                this.syncInputs();
            },

            rebuildAllRooms() {
                const container = document.getElementById('rooms-container');
                container.innerHTML = '';
                this.rooms.forEach((room, i) => {
                    const idx = i + 1;
                    const block = document.createElement('div');
                    block.className = 'room-block';
                    block.dataset.room = idx;
                    const removeBtn = this.rooms.length > 1
                        ? `<button type="button" class="remove-room" onclick="SearchMix.removeRoom(${idx})">Eliminar</button>`
                        : '';
                    block.innerHTML = `
                        <div class="room-header">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--t)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            <span>Habitación ${idx}</span>
                            ${removeBtn}
                        </div>
                        <div class="hub-row">
                            <div class="hub-info"><h4>Adultos</h4></div>
                            <div class="hub-ctrl">
                                <button type="button" class="hub-btn" onclick="SearchMix.adjustRoom(${idx},'adults',-1)">−</button>
                                <span class="hub-num" id="room-${idx}-adults">${room.adults}</span>
                                <button type="button" class="hub-btn" onclick="SearchMix.adjustRoom(${idx},'adults',1)">+</button>
                            </div>
                        </div>
                        <div class="hub-children-list" id="room-${idx}-children"></div>
                        <button type="button" class="add-child-btn" onclick="SearchMix.addChild(${idx})">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Añadir un niño
                        </button>
                    `;
                    container.appendChild(block);
                    this.renderChildren(idx);
                });
            },

            syncInputs() {
                const totalAdults = this.rooms.reduce((s, r) => s + r.adults, 0);
                const totalChildren = this.rooms.reduce((s, r) => s + r.children.length, 0);
                const totalRooms = this.rooms.length;
                const totalGuests = totalAdults + totalChildren;

                document.getElementById('adults-input').value = totalAdults;
                document.getElementById('children-input').value = totalChildren;
                document.getElementById('rooms-input').value = totalRooms;

                let summary = `${totalGuests} huésped${totalGuests > 1 ? 'es' : ''}`;
                summary += ` · ${totalRooms} hab${totalRooms > 1 ? 's' : ''}`;
                
                const summaryEl = document.getElementById('guest-summary');
                summaryEl.innerText = summary;
                summaryEl.classList.remove('empty-styled');
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            const obs = new IntersectionObserver((es) => {
                es.forEach(e => { if (e.isIntersecting) e.target.classList.add('vis'); });
            }, { threshold: 0.1 });
            document.querySelectorAll('.reveal').forEach(x => obs.observe(x));

            // ── PHONE MOCKUP CAROUSEL ──
            const screens = document.querySelectorAll('.mock-screen');
            const dots = document.querySelectorAll('.mock-dot');
            if (screens.length && dots.length) {
                let current = 0;
                const total = screens.length;

                function goToScreen(idx) {
                    screens.forEach((s, i) => {
                        s.classList.remove('active', 'exit');
                        if (i === current) s.classList.add('exit');
                        if (i === idx) s.classList.add('active');
                    });
                    dots.forEach((d, i) => d.classList.toggle('active', i === idx));
                    current = idx;
                }

                setInterval(() => {
                    goToScreen((current + 1) % total);
                }, 3000);
            }
        });
    </script>
@endsection