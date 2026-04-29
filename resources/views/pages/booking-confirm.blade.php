@extends('layouts.app')

@section('content')
  @php
    \Carbon\Carbon::setLocale('es');
    $rate = is_string($booking->rooms_data) ? json_decode($booking->rooms_data, true) : ($booking->rooms_data ?? []);
    
    // RateHawk stores these in 'api_rate' inside 'rooms_data'
    $apiRate = $rate['api_rate'] ?? [];
    $roomName = $apiRate['room_name'] ?? 'Habitación Estándar';
    $meal = $apiRate['meal_label'] ?? ($apiRate['meal'] ?? 'Solo alojamiento');
    
    $nights = $booking->check_in && $booking->check_out
      ? $booking->check_in->diffInDays($booking->check_out)
      : 1;
    $orderId = $booking->ratehawk_order_id ?? $booking->partner_order_id ?? '—';
  @endphp

  <style>
    /* ── PAGE RESET ── */
    #main-header {
      background: #171717 !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    #main-header .logo {
      color: #fff !important;
    }

    #main-header .nav-links a {
      color: rgba(255, 255, 255, 0.85) !important;
    }

    #main-header .nav-right .btn-ghost {
      color: #fff !important;
    }

    /* ── HERO BANNER ── */
    .confirm-hero {
      position: relative;
      min-height: 360px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      padding: 100px 24px 60px;
    }

    .confirm-hero-bg {
      position: absolute;
      inset: 0;
      background: transparent;
      z-index: 0;
    }

    /* Floating orbs */
    .confirm-hero-bg::before {
      content: '';
      position: absolute;
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(238, 108, 77, 0.08) 0%, transparent 70%);
      top: -100px;
      left: -100px;
      border-radius: 50%;
    }

    .confirm-hero-bg::after {
      content: '';
      position: absolute;
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, rgba(129, 178, 154, 0.05) 0%, transparent 70%);
      bottom: -120px;
      right: -80px;
      border-radius: 50%;
    }

    .confirm-hero-content {
      position: relative;
      z-index: 10;
      text-align: center;
      animation: cfadeUp 0.7s ease both;
    }

    /* Animated checkmark ring */
    .check-ring {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: linear-gradient(135deg, #EE6C4D, #f59e78);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 28px;
      box-shadow: 0 0 0 0 rgba(238, 108, 77, 0.4);
      animation: pulseRing 2.5s ease infinite;
      position: relative;
    }

    .check-ring::before {
      content: '';
      position: absolute;
      inset: -6px;
      border-radius: 50%;
      border: 2px solid rgba(238, 108, 77, 0.25);
      animation: spinBorder 8s linear infinite;
    }

    @keyframes spinBorder {
      to {
        transform: rotate(360deg);
      }
    }

    @keyframes pulseRing {
      0% {
        box-shadow: 0 0 0 0 rgba(238, 108, 77, 0.4);
      }

      70% {
        box-shadow: 0 0 0 20px rgba(238, 108, 77, 0);
      }

      100% {
        box-shadow: 0 0 0 0 rgba(238, 108, 77, 0);
      }
    }

    .confirm-title {
      font-family: 'DM Sans', sans-serif;
      font-size: 52px;
      font-weight: 800;
      color: #1a1a1a;
      letter-spacing: -2px;
      line-height: 1;
      margin-bottom: 16px;
    }

    .confirm-title span {
      color: #EE6C4D;
    }

    .confirm-subtitle {
      font-size: 16px;
      color: #666;
      font-weight: 400;
      max-width: 480px;
      margin: 0 auto;
      line-height: 1.6;
    }

    .confirm-subtitle strong {
      color: #EE6C4D;
      font-weight: 600;
    }

    /* ── STATUS CHIP ── */
    .status-chip {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(34, 197, 94, 0.15);
      border: 1px solid rgba(34, 197, 94, 0.3);
      color: #4ade80;
      font-size: 13px;
      font-weight: 700;
      padding: 8px 18px;
      border-radius: 100px;
      margin-bottom: 24px;
      letter-spacing: 0.3px;
      backdrop-filter: blur(10px);
    }

    .status-chip-dot {
      width: 7px;
      height: 7px;
      background: #4ade80;
      border-radius: 50%;
      animation: blink 1.5s ease infinite;
    }

    @keyframes blink {

      0%,
      100% {
        opacity: 1;
      }

      50% {
        opacity: 0.3;
      }
    }

    /* ── MAIN CARD AREA ── */
    .confirm-body {
      max-width: 860px;
      margin: -48px auto 80px;
      padding: 0 24px;
      position: relative;
      z-index: 20;
      animation: cfadeUp 0.7s ease 0.2s both;
    }

    @keyframes cfadeUp {
      from {
        opacity: 0;
        transform: translateY(24px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* ── HOTEL CARD ── */
    .hotel-card {
      background: #fff;
      border-radius: 28px;
      overflow: hidden;
      box-shadow: 0 32px 80px rgba(0, 0, 0, 0.12), 0 4px 16px rgba(0, 0, 0, 0.06);
      border: 1px solid rgba(0, 0, 0, 0.04);
      margin-bottom: 20px;
    }

    .hotel-img-wrap {
      position: relative;
      height: 240px;
      overflow: hidden;
    }

    .hotel-img-wrap img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 8s ease;
    }

    .hotel-img-wrap:hover img {
      transform: scale(1.04);
    }

    .hotel-img-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(to top, rgba(0, 0, 0, 0.5) 0%, transparent 50%);
    }

    .hotel-img-badge {
      position: absolute;
      top: 20px;
      left: 20px;
      background: rgba(238, 108, 77, 0.92);
      backdrop-filter: blur(8px);
      color: #fff;
      font-size: 11px;
      font-weight: 800;
      padding: 6px 16px;
      border-radius: 100px;
      letter-spacing: 0.8px;
      text-transform: uppercase;
    }

    .hotel-img-order {
      position: absolute;
      bottom: 20px;
      left: 20px;
      color: #fff;
      font-size: 12px;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .hotel-img-order-id {
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(8px);
      padding: 6px 14px;
      border-radius: 8px;
      font-family: 'DM Mono', monospace;
      letter-spacing: 1px;
      border: 1px solid rgba(255, 255, 255, 0.15);
      font-size: 13px;
      color: #fff;
    }

    .hotel-body {
      padding: 32px;
    }

    .hotel-header-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
      margin-bottom: 28px;
      padding-bottom: 28px;
      border-bottom: 1px solid #f0ede8;
    }

    .hotel-stars {
      font-size: 15px;
      color: #EE6C4D;
      margin-bottom: 8px;
    }

    .hotel-name {
      font-family: 'DM Sans', sans-serif;
      font-size: 26px;
      font-weight: 800;
      color: #1a1a1a;
      letter-spacing: -0.5px;
      line-height: 1.2;
      margin-bottom: 6px;
    }

    .hotel-addr {
      font-size: 13px;
      color: #888;
      display: flex;
      align-items: center;
      gap: 6px;
      font-weight: 500;
    }

    .confirmed-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #dcfce7;
      color: #16a34a;
      font-size: 12px;
      font-weight: 700;
      padding: 8px 16px;
      border-radius: 100px;
      border: 1px solid #bbf7d0;
      white-space: nowrap;
      flex-shrink: 0;
    }

    /* ── DETAILS GRID (3 cols) ── */
    .details-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1px;
      background: #f0ede8;
      border-top: 1px solid #f0ede8;
      border-bottom: 1px solid #f0ede8;
      margin: 32px -32px 0;
    }

    .detail-cell {
      background: #fff;
      padding: 16px 24px;
      text-align: left;
      border-right: 1px solid #f0ede8;
    }

    .detail-cell:last-child {
      border-right: none;
    }

    .dc-label {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #aaa;
      margin-bottom: 12px;
    }

    .dc-value {
      font-size: 20px;
      font-weight: 700;
      color: #1a1a1a;
      margin-bottom: 2px;
      line-height: 1;
    }

    .dc-meta {
      font-size: 13px;
      color: #999;
      font-weight: 400;
    }

    .dc-value.accent {
      color: #EE6C4D;
    }

    /* ── GUESTS SECTION ── */
    .guests-section {
      padding: 32px 0 0;
    }

    .gs-label {
      font-size: 11px;
      font-weight: 700;
      color: #aaa;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 16px;
    }

    .gs-summary {
      font-size: 20px;
      font-weight: 700;
      color: #1a1a1a;
      margin-bottom: 24px;
    }

    .dist-grid {
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
    }

    .dist-box {
      flex: 1;
      min-width: 180px;
      background: #f6f6f6;
      padding: 12px 20px;
      border-radius: 12px;
    }

    .db-label {
      font-size: 11px;
      color: #999;
      font-weight: 700;
      text-transform: uppercase;
      margin-bottom: 4px;
      letter-spacing: 0.5px;
    }

    .db-val {
      font-size: 15px;
      font-weight: 700;
      color: #1a1a1a;
    }

    /* ── ROOM & MEAL ── */
    .rate-row {
      display: flex;
      gap: 16px;
      margin-bottom: 28px;
    }

    .rate-pill {
      flex: 1;
      display: flex;
      align-items: center;
      gap: 14px;
      background: #faf9f7;
      border: 1px solid #ece9e4;
      border-radius: 16px;
      padding: 18px 20px;
    }

    .rate-pill-icon {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      flex-shrink: 0;
    }

    .rate-pill-icon.orange {
      background: #fdf0eb;
    }

    .rate-pill-icon.green {
      background: #f0fdf4;
    }

    .rate-pill-label {
      font-size: 11px;
      font-weight: 700;
      color: #aaa;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-bottom: 3px;
    }

    .rate-pill-value {
      font-size: 14px;
      font-weight: 700;
      color: #1a1a1a;
    }

    /* ── PRICE BOX ── */
    .price-summary {
      background: transparent;
      border-radius: 20px;
      padding: 24px 28px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      border: 1px solid #ece9e4;
    }

    .price-summary-left {}

    .price-summary-label {
      font-size: 12px;
      color: #888;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 4px;
    }

    .price-summary-value {
      font-size: 38px;
      font-weight: 800;
      color: #EE6C4D;
      letter-spacing: -1.5px;
      line-height: 1;
    }

    .price-summary-nights {
      font-size: 13px;
      color: #666;
      margin-top: 4px;
    }

    .price-summary-right {
      text-align: right;
    }

    .price-per-night-label {
      font-size: 11px;
      color: #888;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin-bottom: 4px;
    }

    .price-per-night {
      font-size: 22px;
      font-weight: 700;
      color: #1a1a1a;
    }

    /* ── NEXT STEPS ── */
    .steps-card {
      background: #fff;
      border-radius: 24px;
      padding: 28px 32px;
      box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
      border: 1px solid rgba(0, 0, 0, 0.04);
      margin-bottom: 20px;
    }

    .steps-title {
      font-size: 13px;
      font-weight: 700;
      color: #888;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 20px;
    }

    .step-item {
      display: flex;
      align-items: flex-start;
      gap: 16px;
      padding: 14px 0;
      border-bottom: 1px solid #f5f3f0;
    }

    .step-item:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }

    .step-num {
      width: 32px;
      height: 32px;
      background: #fdf0eb;
      color: #EE6C4D;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
      font-weight: 800;
      flex-shrink: 0;
    }

    .step-text {
      font-size: 14px;
      color: #555;
      line-height: 1.6;
    }

    .step-text strong {
      color: #1a1a1a;
    }

    /* ── CTA BUTTONS ── */
    .cta-row {
      display: flex;
      gap: 14px;
      margin-bottom: 40px;
    }

    .cta-secondary {
      flex: 1;
      padding: 18px;
      font-size: 14px;
      font-weight: 700;
      text-align: center;
      border: 2px solid #e8e4de;
      border-radius: 16px;
      color: #555;
      text-decoration: none;
      background: #fff;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .cta-secondary:hover {
      border-color: #EE6C4D;
      color: #EE6C4D;
      background: #fff8f6;
    }

    .cta-primary {
      flex: 2;
      padding: 18px;
      font-size: 15px;
      font-weight: 700;
      text-align: center;
      border-radius: 16px;
      background: #EE6C4D;
      color: #fff;
      text-decoration: none;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      box-shadow: 0 6px 20px rgba(238, 108, 77, 0.3);
    }

    .cta-primary:hover {
      background: #D35B3E;
      transform: translateY(-2px);
      box-shadow: 0 10px 28px rgba(238, 108, 77, 0.35);
    }

    /* ── FOOTER NOTE ── */
    .footer-note {
      text-align: center;
      font-size: 13px;
      color: #bbb;
      font-weight: 500;
      padding-bottom: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    /* Responsive */
    @media(max-width: 768px) {
      .confirm-title {
        font-size: 34px;
      }

      .details-grid {
        grid-template-columns: 1fr 1fr;
      }

      .rate-row {
        flex-direction: column;
      }

      .price-summary {
        flex-direction: column;
        text-align: center;
      }

      .price-summary-right {
        text-align: center;
      }

      .cta-row {
        flex-direction: column;
      }

      .hotel-header-row {
        flex-direction: column;
      }
    }
  </style>

  {{-- ══════════════ HERO SECTION ══════════════ --}}
  <div class="confirm-hero">
    <div class="confirm-hero-bg"></div>
    <div class="confirm-hero-content">
      <div class="check-ring">
        <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"
          stroke-linecap="round" stroke-linejoin="round">
          <polyline points="20 6 9 17 4 12" />
        </svg>
      </div>
      <h1 class="confirm-title">¡Tu nido está <span>listo</span>!</h1>
      <p class="confirm-subtitle">
        Hemos enviado la confirmación instantánea a
        <strong>{{ $booking->guest_email }}</strong>.<br>
      </p>
    </div>
  </div>

  {{-- ══════════════ CARD BODY ══════════════ --}}
  <div class="confirm-body">

    {{-- ── HOTEL CARD ── --}}
    <div class="hotel-card">

      {{-- Hotel Image --}}
      @if($booking->hotel_image)
        <div class="hotel-img-wrap">
          <img src="{{ $booking->hotel_image }}" alt="{{ $booking->hotel_name }}">
          <div class="hotel-img-overlay"></div>
          <div class="hotel-img-badge">
            ✦ {{ $booking->hotel_stars ? $booking->hotel_stars . ' Estrellas' : 'Hotel' }}
          </div>
        </div>
      @endif

      <div class="hotel-body">

        {{-- Hotel Name & Badge --}}
        <div class="hotel-header-row">
          <div>
            <div class="hotel-stars">{{ str_repeat('★', (int) $booking->hotel_stars) }}</div>
            <div class="hotel-name">{{ $booking->hotel_name }}</div>
            <div class="hotel-addr">
              <svg width="12" height="12" viewBox="0 0 14 14" fill="none">
                <path
                  d="M7 1C4.2 1 2 3.2 2 6c0 3.5 5 8 5 8s5-4.5 5-8c0-2.8-2.2-5-5-5zm0 6.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"
                  fill="#EE6C4D" />
              </svg>
              {{ $booking->hotel_address ?: ($booking->hotel_city . ', ' . $booking->hotel_country) }}
            </div>
            <div style="margin-top: 14px; font-size: 14px; color: #555;">
              <span style="font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; color: #888; margin-right: 6px;">Nº Referencia</span>
              <span style="font-family: 'DM Mono', monospace; color: #EE6C4D; font-weight: 700; font-size: 16px; background: #fdf0eb; padding: 4px 10px; border-radius: 6px;">#{{ $orderId }}</span>
            </div>
          </div>
          <div class="confirmed-badge">
            <svg width="13" height="13" viewBox="0 0 14 14" fill="none">
              <polyline points="11.5 3.5 5.5 10.5 2.5 7.5" stroke="#16a34a" stroke-width="2" stroke-linecap="round" />
            </svg>
            Confirmada
          </div>
        </div>

        {{-- Dates Grid (3 columns) --}}
        <div class="details-grid">
          <div class="detail-cell">
            <div class="dc-label">Check-in</div>
            <div class="dc-value">{{ \Carbon\Carbon::parse($booking->check_in)->locale('es')->translatedFormat('d M') }}</div>
            <div class="dc-meta">{{ \Carbon\Carbon::parse($booking->check_in)->format('Y') }} · {{ ucfirst(\Carbon\Carbon::parse($booking->check_in)->locale('es')->translatedFormat('l')) }}
            </div>
          </div>
          <div class="detail-cell">
            <div class="dc-label">Check-out</div>
            <div class="dc-value">{{ \Carbon\Carbon::parse($booking->check_out)->locale('es')->translatedFormat('d M') }}</div>
            <div class="dc-meta">{{ \Carbon\Carbon::parse($booking->check_out)->format('Y') }} ·
              {{ ucfirst(\Carbon\Carbon::parse($booking->check_out)->locale('es')->translatedFormat('l')) }}</div>
          </div>
          <div class="detail-cell">
            <div class="dc-label">Duración</div>
            <div class="dc-value accent">{{ $nights }}</div>
            <div class="dc-meta">noches</div>
          </div>
        </div>

        {{-- Guests Section --}}
        <div class="guests-section">
          @php 
              $rConf = $rate['rooms_config'] ?? null;
              if (is_string($rConf)) {
                  $rConf = json_decode(urldecode($rConf), true);
              }
              
              // Use booking attributes as source of truth for the main summary
              $totalAdults = $booking->guests;
              $totalChildren = $booking->children;
              $roomsCount = $booking->rooms;
          @endphp
          
          <div class="summary-row" style="display:flex; justify-content:space-between; align-items:flex-start; padding: 16px 0; border-top: 1px solid #f0ede8;">
            <span class="sr-label" style="font-size: 13px; font-weight: 700; color: #aaa; text-transform: uppercase; letter-spacing: 0.5px;">Huéspedes</span>
            <div id="summary-guests" style="display:flex; flex-direction:column; align-items:flex-end; flex:1; margin-left:20px;">
              <div style="font-weight:700; color:#1a1a1a; font-size: 16px;">
                {{ $totalAdults }} adulto{{ $totalAdults > 1 ? 's' : '' }}
                @if($totalChildren > 0) · {{ $totalChildren }} niño{{ $totalChildren > 1 ? 's' : '' }} @endif
                · {{ $roomsCount }} hab.
              </div>
              @if(is_array($rConf) && count($rConf) > 0)
                <div style="margin-top:6px; width:100%;">
                  @foreach($rConf as $i => $r)
                    <div style="font-size:12px; color:#1a1a1a; margin-top:4px; display:flex; justify-content:flex-end; gap:8px;">
                      <span style="color:#888;">Hab {{ $i+1 }}</span>
                      <span style="font-weight:600;">
                        {{ $r['adults'] }} adulto{{ $r['adults'] > 1 ? 's' : '' }}
                        @if(isset($r['children']) && count($r['children']) > 0)
                          · {{ count($r['children']) }} niño{{ count($r['children']) > 1 ? 's' : '' }}
                        @endif
                      </span>
                    </div>
                  @endforeach
                </div>
              @endif
            </div>
          </div>

          <div style="height: 1px; background: #f0ede8; margin: 4px 0;"></div>

          <div class="summary-row" style="display:flex; justify-content:space-between; align-items:center; padding: 16px 0;">
            <span class="sr-label" style="font-size: 13px; font-weight: 700; color: #e85d2f; text-transform: uppercase; letter-spacing: 0.5px;">Habitación</span>
            <span class="sr-value" style="font-size: 16px; font-weight: 700; color: #1a1a1a; text-align: right;">{{ $roomName }}</span>
          </div>

          <div class="summary-row" style="display:flex; justify-content:space-between; align-items:center; padding: 16px 0; border-top: 1px solid #f5f5f5;">
            <span class="sr-label" style="font-size: 13px; font-weight: 700; color: #e85d2f; text-transform: uppercase; letter-spacing: 0.5px;">Régimen</span>
            <span class="sr-value" id="summary-meal" style="font-size: 16px; font-weight: 700; color: #1a1a1a; text-align: right;">{{ $meal }}</span>
          </div>
        </div>

        {{-- Price Summary --}}
        <div class="price-summary">
          <div class="price-summary-left">
            <div class="price-summary-label">Precio total pagado</div>
            <div class="price-summary-value">$ {{ number_format($booking->total_price, 0) }}</div>
            <div class="price-summary-nights">{{ $booking->currency ?? 'USD' }} · {{ $nights }}
              noche{{ $nights != 1 ? 's' : '' }} · IVA incluido</div>
          </div>
          @if($nights > 0)
            <div class="price-summary-right">
              <div class="price-per-night-label">Por noche</div>
              <div class="price-per-night">$ {{ number_format($booking->total_price / $nights, 0) }}</div>
            </div>
          @endif
        </div>

      </div>
    </div>

    {{-- ── NEXT STEPS CARD ── --}}
    <div class="steps-card">
      <div class="steps-title">¿Qué sigue ahora?</div>
      <div class="step-item">
        <div class="step-num">1</div>
        <div class="step-text">
          <strong>Revisa tu correo</strong> — Te hemos enviado todos los detalles a
          <strong>{{ $booking->guest_email }}</strong> incluyendo tu comprobante de reserva.
        </div>
      </div>
      <div class="step-item">
        <div class="step-num">2</div>
        <div class="step-text">
          <strong>Guarda tu referencia</strong> — Tu número de reserva es <strong
            style="color:#EE6C4D; font-family:monospace;">#{{ $orderId }}</strong>. Preséntalo en el check-in del hotel.
        </div>
      </div>
      <div class="step-item">
        <div class="step-num">3</div>
        <div class="step-text">
          <strong>Check-in el {{ \Carbon\Carbon::parse($booking->check_in)->locale('es')->translatedFormat('j \d\e F, Y') }}</strong> — El horario estándar de
          check-in es a las 14:00h. Contacta al hotel si necesitas una llegada anticipada.
        </div>
      </div>
    </div>

    {{-- ── CTA ROW ── --}}
    <div class="cta-row">
      <a href="{{ route('home') }}" class="cta-secondary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round">
          <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
          <polyline points="9 22 9 12 15 12 15 22" />
        </svg>
        Inicio
      </a>
      @if(Auth::check())
        <a href="{{ route('dashboard.bookings') }}" class="cta-primary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round">
            <rect x="3" y="4" width="18" height="18" rx="2" />
            <line x1="16" y1="2" x2="16" y2="6" />
            <line x1="8" y1="2" x2="8" y2="6" />
            <line x1="3" y1="10" x2="21" y2="10" />
          </svg>
          Ver todas mis reservas
        </a>
      @else
        <a href="{{ route('register') }}" class="cta-primary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round">
            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
            <circle cx="12" cy="7" r="4" />
          </svg>
          Crea tu cuenta y gestiona tu reserva
        </a>
      @endif
    </div>

    {{-- ── FOOTER NOTE ── --}}
    <div class="footer-note">
      <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
        <path d="M7 1L2 3v4c0 3.1 2.1 5.8 5 6.7 2.9-.9 5-3.6 5-6.7V3L7 1z" stroke="#bbb" stroke-width="1.2" fill="none" />
      </svg>
      Reserva protegida por Nestay · Powered by Viajes GPS
    </div>

  </div>
@endsection