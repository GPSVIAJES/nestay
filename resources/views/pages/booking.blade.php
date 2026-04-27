@extends('layouts.app')

@section('header_search')
    <div style="display:flex; justify-content:flex-end; width:100%;">
        <button onclick="history.back()" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #fff; font-size: 13px; padding: 8px 16px; cursor: pointer; transition: all 0.2s; font-family:'DM Sans',sans-serif;">
            ← Volver al hotel
        </button>
    </div>
@endsection

@section('content')
<style>
  /* RESULTS SPECIFIC HEADER THEME */
  #main-header {
    background: #171717 !important;
    color: #fff !important;
    border-bottom: 1px solid rgba(255,255,255,0.05);
  }
  #main-header .logo { color: #fff !important; }
  #main-header .nav-right .btn-ghost { color: #fff !important; }

  /* Checkout specific styles */
  .checkout-page { font-family: 'DM Sans', sans-serif; background: #faf9f7; color: #1a1a1a; min-height: 100vh; padding-bottom: 80px; }

  /* PROGRESS */
  .progress-bar { background: #fff; border-bottom: 0.5px solid #ece9e4; padding: 16px 32px; display: flex; align-items: center; justify-content: center; gap: 0; }
  .step { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #bbb; }
  .step.active { color: #1a1a1a; font-weight: 600; }
  .step.done { color: #22a87a; }
  .step-num { width: 26px; height: 26px; border-radius: 50%; background: #f0ede8; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: #bbb; flex-shrink: 0; transition: all 0.3s; }
  .step.active .step-num { background: #e85d2f; color: #fff; }
  .step.done .step-num { background: #22a87a; color: #fff; font-size: 15px; }
  .step-line { width: 56px; height: 2px; background: #e0ddd8; margin: 0 12px; transition: background 0.3s; border-radius: 2px; }
  .step-line.done { background: #22a87a; }

  /* LAYOUT - MADE WIDER AND MORE PREMIUM */
  .checkout-wrap { max-width: 1200px; margin: 0 auto; padding: 48px 24px 80px; display: flex; gap: 40px; align-items: flex-start; }
  .checkout-left { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 20px; }
  .checkout-right { width: 380px; flex-shrink: 0; position: sticky; top: 120px; }

  .page-title { font-family: 'Playfair Display', serif; font-size: 32px; color: #1a1a1a; margin-bottom: 6px; }
  .page-sub { font-size: 15px; color: #888; margin-bottom: 12px; }

  .ccard { background: #fff; border: 0.5px solid #e8e4de; border-radius: 20px; padding: 28px; box-shadow: 0 4px 24px rgba(0,0,0,0.02); }
  .ccard-header { display: flex; align-items: center; gap: 14px; margin-bottom: 24px; }
  .ccard-icon { width: 44px; height: 44px; background: #fdf0eb; color: #e85d2f; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
  .ccard-title { font-size: 17px; font-weight: 700; }
  .ccard-sub { font-size: 13px; color: #999; margin-top: 2px; }

  .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
  .form-grid:last-child { margin-bottom: 0; }
  .cfield { display: flex; flex-direction: column; gap: 8px; }
  .cfield label { font-size: 12px; text-transform: uppercase; letter-spacing: 0.8px; color: #999; font-weight: 600; }
  .cfield input, .cfield textarea { border: 1.5px solid #eaeaea; border-radius: 12px; padding: 14px 16px; font-size: 15px; color: #1a1a1a; background: #faf9f7; outline: none; font-family: 'DM Sans', sans-serif; transition: all 0.2s; }
  .cfield input:focus, .cfield textarea:focus { border-color: #e85d2f; background: #fff; box-shadow: 0 0 0 4px rgba(232,93,47,0.08); }
  .cfield input::placeholder, .cfield textarea::placeholder { color: #bbb; }
  .cfield textarea { resize: vertical; min-height: 100px; }

  .terms-card { background: #fff; border: 0.5px solid #e8e4de; border-radius: 20px; padding: 24px 28px; box-shadow: 0 4px 24px rgba(0,0,0,0.02); }
  .terms-row { display: flex; align-items: flex-start; gap: 14px; margin-bottom: 18px; cursor: pointer; }
  .terms-row:last-child { margin-bottom: 0; }
  .checkbox-custom { width: 22px; height: 22px; border: 1.5px solid #ddd; border-radius: 6px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; margin-top: 1px; transition: all 0.15s; background: #faf9f7; }
  .checkbox-custom.checked { background: #e85d2f; border-color: #e85d2f; }
  .checkbox-custom.checked::after { content: '✓'; color: #fff; font-size: 13px; font-weight: 700; }
  .terms-text { font-size: 14px; color: #555; line-height: 1.6; }
  .terms-text a { color: #e85d2f; text-decoration: none; font-weight: 600; }

  .cta-wrap { display: flex; flex-direction: column; gap: 12px; margin-top: 8px; }
  .btn-cta { width: 100%; background: #e85d2f; color: #fff; border: none; border-radius: 16px; padding: 18px; font-size: 17px; font-weight: 700; cursor: pointer; transition: all 0.2s; letter-spacing: -0.2px; }
  .btn-cta:hover { background: #d14f24; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(232,93,47,0.25); }
  .btn-cta:disabled { background: #ccc; cursor: default; transform:none; box-shadow:none; }
  .secure-note { display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px; color: #aaa; margin-top:6px; font-weight: 500; }

  /* ── STRIPE STEP ── */
  .stripe-wrapper { display: none; flex-direction: column; gap: 20px; }
  .stripe-wrapper.visible { display: flex; animation: fadeUp 0.4s ease both; }
  @keyframes fadeUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
  .step1-wrapper { display: flex; flex-direction: column; gap: 20px; }
  .step1-wrapper.hidden { display: none; }

  .stripe-card { background: #fff; border: 0.5px solid #e8e4de; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.04); }
  .stripe-header { background: #635bff; padding: 20px 28px; display: flex; align-items: center; justify-content: space-between; }
  .stripe-logo { display: flex; align-items: center; gap: 12px; }
  .stripe-logo-mark { background: #fff; border-radius: 8px; padding: 5px 10px; font-size: 15px; font-weight: 800; color: #635bff; letter-spacing: -0.5px; }
  .stripe-tagline { font-size: 13px; color: rgba(255,255,255,0.85); font-weight: 500; }
  .stripe-locks { display: flex; align-items: center; gap: 8px; font-size: 12px; color: rgba(255,255,255,0.8); font-weight: 500; }

  .stripe-body { padding: 28px; }
  .stripe-amount-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #f0ede8; }
  .stripe-merchant { font-size: 14px; color: #888; }
  .stripe-merchant strong { color: #1a1a1a; font-size: 16px; display: block; margin-bottom: 4px; font-weight: 700; }
  .stripe-total { text-align: right; }
  .stripe-total-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; color: #aaa; font-weight: 600; margin-bottom: 4px; }
  .stripe-total-amount { font-size: 32px; font-weight: 800; color: #635bff; }
  .stripe-total-currency { font-size: 14px; color: #aaa; font-weight: 600; }

  .stripe-field { margin-bottom: 16px; }
  .stripe-field label { font-size: 12px; text-transform: uppercase; letter-spacing: 0.8px; color: #6b7280; font-weight: 600; display: block; margin-bottom: 6px; }
  .stripe-input-wrap { border: 1.5px solid #e5e7eb; border-radius: 12px; padding: 14px 16px; background: #fff; display: flex; align-items: center; gap: 12px; transition: border-color 0.2s; cursor: text; }
  .stripe-input-wrap:focus-within { border-color: #635bff; box-shadow: 0 0 0 4px rgba(99,91,255,0.12); }
  .stripe-input-wrap input { border: none; outline: none; font-size: 16px; color: #1a1a1a; flex: 1; font-family: 'DM Sans', sans-serif; background: transparent; letter-spacing: 1px; }
  .stripe-input-wrap input::placeholder { color: #9ca3af; letter-spacing: normal; }
  .card-brand-icons { display: flex; gap: 6px; }
  .brand-icon { font-size: 20px; opacity: 0.4; transition: opacity 0.2s; }
  .brand-icon.active { opacity: 1; }
  .stripe-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .stripe-lock-note { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #9ca3af; margin-top: 6px; font-weight: 500; }

  .btn-stripe-pay { width: 100%; background: #635bff; color: #fff; border: none; border-radius: 14px; padding: 18px; font-size: 17px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.2s; margin-top: 10px; }
  .btn-stripe-pay:hover { background: #4f46e5; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(99,91,255,0.25); }

  .stripe-footer { padding: 14px 28px; background: #f9fafb; border-top: 1px solid #f0ede8; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 12px; color: #9ca3af; font-weight: 500; }

  .btn-back { background: none; border: none; color: #888; font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 6px; padding: 0; transition: color 0.2s; }
  .btn-back:hover { color: #e85d2f; }

  /* RIGHT SUMMARY */
  .summary-card { background: #fff; border: 0.5px solid #e8e4de; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.02); }
  .summary-img { height: 180px; background: linear-gradient(135deg, #b4c8b8 0%, #8fa89a 100%); display: flex; align-items: center; justify-content: center; font-size: 64px; position: relative; }
  .summary-badge { position: absolute; top: 16px; left: 16px; background: rgba(28,28,28,0.85); color: #fff; font-size: 11px; font-weight: 700; padding: 6px 14px; border-radius: 24px; letter-spacing: 0.5px; backdrop-filter: blur(4px); }
  .summary-stars { position: absolute; top: 16px; right: 16px; color: #e85d2f; font-size: 13px; background: rgba(255,255,255,0.95); padding: 5px 12px; border-radius: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
  .summary-body { padding: 24px 28px; }
  .summary-name { font-family: 'Playfair Display', serif; font-size: 20px; margin-bottom: 6px; font-weight: 700; line-height: 1.2; }
  .summary-location { font-size: 13px; color: #888; display: flex; align-items: center; gap: 6px; margin-bottom: 24px; font-weight: 500; }
  .summary-rows { display: flex; flex-direction: column; border: 1px solid #ece9e4; border-radius: 14px; overflow: hidden; margin-bottom: 24px; }
  .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; font-size: 14px; border-bottom: 1px solid #ece9e4; }
  .summary-row:last-child { border-bottom: none; }
  .sr-label { color: #888; font-weight: 500; }
  .sr-value { font-weight: 600; color: #1a1a1a; }
  .price-box { background: #fdf0eb; border-radius: 16px; padding: 20px; text-align: center; margin-bottom: 20px; }
  .price-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1.2px; color: #c44a1f; font-weight: 700; margin-bottom: 6px; }
  .price-big { font-size: 42px; font-weight: 800; color: #e85d2f; line-height: 1; letter-spacing: -1px; }
  .price-nights { font-size: 13px; color: #c44a1f; margin-top: 6px; font-weight: 500; }
  .cancellation-note { display: flex; align-items: flex-start; gap: 10px; background: #eaf3de; border-radius: 12px; padding: 14px 16px; margin-top: 16px; }
  .cn-text { font-size: 13px; color: #3b6d11; line-height: 1.5; font-weight: 500; }

  /* guest summary pill in step 2 */
  .guest-pill { background: #f5f3f0; border-radius: 16px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; font-size: 14px; color: #555; border: 1px solid #e8e4de; }
  .guest-pill-avatar { width: 40px; height: 40px; border-radius: 50%; background: #fdf0eb; color: #c44a1f; display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 700; flex-shrink: 0; }
  .guest-pill-name { font-weight: 600; color: #1a1a1a; font-size: 16px; margin-bottom: 2px; }
  .guest-pill-edit { margin-left: auto; color: #e85d2f; font-size: 13px; cursor: pointer; font-weight: 600; padding: 6px 12px; border-radius: 8px; transition: background 0.2s; }
  .guest-pill-edit:hover { background: rgba(232,93,47,0.1); }
</style>

<div class="checkout-page">
  <!-- PROGRESS -->
  <div class="progress-bar">
    <div class="step done" id="prog1"><div class="step-num" id="pn1">✓</div> Selección</div>
    <div class="step-line done" id="pl1"></div>
    <div class="step active" id="prog2"><div class="step-num" id="pn2">2</div> Tus datos</div>
    <div class="step-line" id="pl2"></div>
    <div class="step" id="prog3"><div class="step-num" id="pn3">3</div> Confirmación</div>
  </div>

  <div class="checkout-wrap">
    <div class="checkout-left">
      <!-- Status Banner (Price changes, errors) -->
      <div id="prebook-status" style="display:none; padding:16px 20px; border-radius:16px; margin-bottom:24px; font-size:14px; animation:fadeUp 0.4s ease both;"></div>

      @if(!Auth::check())
      <div style="background: #fef9c3; border: 1px solid #fde047; border-radius: 16px; padding: 18px 24px; margin-bottom: 24px; display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 12px rgba(253, 224, 71, 0.15);">
        <div style="width: 40px; height: 40px; background: #fef08a; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 20px;">
          🎁
        </div>
        <div>
          <div style="font-size: 15px; font-weight: 700; color: #854d0e; margin-bottom: 2px;">¿Quieres más beneficios en tu reserva?</div>
          <div style="font-size: 13px; color: #a16207; line-height: 1.4;">
            Si inicias sesión podrás acumular noches y gestionar tu reserva más fácil. <a href="{{ route('login') }}" style="color: #ca8a04; font-weight: 700; text-decoration: underline;">Inicia sesión aquí</a> para continuar.
          </div>
        </div>
      </div>
      @endif

      <form id="booking-form" onsubmit="handlePaySubmit(event)">
        @csrf
        
        <!-- ══ STEP 1: GUEST DATA ══ -->
        <div class="step1-wrapper" id="step1">
          <div>
            <div class="page-title">Completa tu próximo nido</div>
            <div class="page-sub">Solo te tomará un momento. Reserva confirmada al instante.</div>
          </div>

          <div class="ccard">
            <div class="ccard-header">
              <div class="ccard-icon">👤</div>
              <div>
                <div class="ccard-title">Datos del huésped principal</div>
                <div class="ccard-sub">Quien realizará el check-in en el hotel</div>
              </div>
            </div>
            <div class="form-grid">
              <div class="cfield"><label>Nombre</label><input id="first_name" type="text" value="{{ Auth::user()->name ?? '' }}" required></div>
              <div class="cfield"><label>Apellido</label><input id="last_name" type="text" required></div>
            </div>
            <div class="form-grid">
              <div class="cfield"><label>Email de confirmación</label><input id="email" type="email" value="{{ Auth::user()->email ?? '' }}" required></div>
              <div class="cfield"><label>Teléfono móvil</label><input id="phone" type="tel" placeholder="+00 000 000 000"></div>
            </div>
            <div class="form-grid" style="grid-template-columns: 1fr; margin-top: 16px;">
              <div class="cfield">
                <label>Petición especial (Opcional)</label>
                <textarea id="hotel_notes" rows="3" placeholder="Ej. Llegada tardía, alergias, petición de cama extra, etc..."></textarea>
              </div>
            </div>
          </div>

          <div class="terms-card">
            <div class="terms-row" onclick="toggleCheckbox('t1')">
              <div class="checkbox-custom" id="t1"></div>
              <div class="terms-text">Acepto los <a href="#">Términos y Condiciones</a> y la <a href="#">Política de Privacidad</a> de Nestay. Entiendo que esta es una reserva con confirmación inmediata.</div>
            </div>
            <div class="terms-row" onclick="toggleCheckbox('t2')">
              <div class="checkbox-custom" id="t2"></div>
              <div class="terms-text">Acepto recibir comunicaciones sobre mi reserva y ofertas personalizadas de Nestay.</div>
            </div>
          </div>

          <div class="cta-wrap">
            <button type="submit" id="submit-booking-btn" class="btn-cta">Confirmar Reserva →</button>
            <div class="secure-note">
              <svg width="13" height="13" viewBox="0 0 14 14" fill="none" style="margin-top:-2px;"><path d="M7 1L2 3v4c0 3.1 2.1 5.8 5 6.7 2.9-.9 5-3.6 5-6.7V3L7 1z" stroke="#aaa" stroke-width="1.2" fill="none"/></svg>
              Transacción 100% segura y encriptada
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- RIGHT: booking summary -->
    <div class="checkout-right">
      <div class="summary-card">
        <div class="summary-img">
          <img id="summary-img" style="width:100%; height:100%; object-fit:cover;" alt="Hotel">
          <div class="summary-badge" id="summary-badge">5 estrellas</div>
          <div class="summary-stars" id="summary-stars">★★★★★</div>
        </div>
        <div class="summary-body">
          <div class="summary-name" id="summary-name">...</div>
          <div class="summary-location" id="summary-location">
            <svg width="10" height="10" viewBox="0 0 14 14" fill="none"><path d="M7 1C4.2 1 2 3.2 2 6c0 3.5 5 8 5 8s5-4.5 5-8c0-2.8-2.2-5-5-5zm0 6.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z" fill="#e85d2f"/></svg>
            Cargando...
          </div>
          <div class="summary-rows">
            <div class="summary-row"><span class="sr-label">Check-in</span><span class="sr-value" id="summary-in" style="font-weight: 600; color: #1a1a1a;">...</span></div>
            <div class="summary-row"><span class="sr-label">Check-out</span><span class="sr-value" id="summary-out" style="font-weight: 600; color: #1a1a1a;">...</span></div>
            <div class="summary-row"><span class="sr-label">Huéspedes</span><span class="sr-value" id="summary-guests" style="font-weight: 600; color: #1a1a1a;">...</span></div>
            <div style="height: 1px; background: #eaeaea; margin: 12px 0;"></div>
            <div class="summary-row"><span class="sr-label" style="color: #e85d2f; font-weight: 700;">Habitación</span><span class="sr-value" id="summary-room" style="font-weight: 700; color: #1a1a1a;">Estándar</span></div>
            <div class="summary-row"><span class="sr-label" style="color: #e85d2f; font-weight: 700;">Régimen</span><span class="sr-value" id="summary-meal" style="font-weight: 700; color: #1a1a1a;">Solo alojamiento</span></div>
          </div>
          <div class="price-box">
            <div class="price-label">Precio final</div>
            <div class="price-big" id="summary-price">$0</div>
            <div class="price-nights" id="summary-nights">...</div>
          </div>
          <div class="cancellation-note">
            <span style="font-size:14px;flex-shrink:0;color:#54991c;">✓</span>
            <span class="cn-text" id="summary-cancel">Cancelación gratuita según políticas del hotel.</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
    const bookingParams = new URLSearchParams(window.location.search);

    document.addEventListener('DOMContentLoaded', () => {
        // Populate Summary
        const name = bookingParams.get('hotel_name');
        const img = bookingParams.get('hotel_image');
        const stars = parseInt(bookingParams.get('hotel_stars')) || 4;
        const checkin = bookingParams.get('check_in');
        const checkout = bookingParams.get('check_out');
        const guests = bookingParams.get('guests');
        const price = bookingParams.get('total_price');
        const roomName = bookingParams.get('room_name') || 'Habitación';
        const mealLabel = bookingParams.get('meal_label') || 'Solo alojamiento';
        const address = bookingParams.get('hotel_address') || 'Dirección no disponible';
        const refundable = bookingParams.get('refundable') === '1';
        
        document.getElementById('summary-name').textContent = name;
        if(img) document.getElementById('summary-img').src = img;
        document.getElementById('summary-stars').textContent = '★'.repeat(stars);
        document.getElementById('summary-badge').textContent = `${stars} estrellas`;
        document.getElementById('summary-location').innerHTML = `<svg width="10" height="10" viewBox="0 0 14 14" fill="none"><path d="M7 1C4.2 1 2 3.2 2 6c0 3.5 5 8 5 8s5-4.5 5-8c0-2.8-2.2-5-5-5zm0 6.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z" fill="#e85d2f"/></svg> ${address}`;
        
        document.getElementById('summary-in').textContent = window.formatDate ? window.formatDate(checkin) : checkin;
        document.getElementById('summary-out').textContent = window.formatDate ? window.formatDate(checkout) : checkout;
        
        const children = parseInt(bookingParams.get('children')) || 0;
        const roomsCount = parseInt(bookingParams.get('rooms')) || 1;
        let gSummary = `${guests} adulto${guests > 1 ? 's' : ''}`;
        if (children > 0) gSummary += ` · ${children} niño${children > 1 ? 's' : ''}`;
        gSummary += ` · ${roomsCount} hab.`;

        // Clear previous breakdown if any
        const guestValEl = document.getElementById('summary-guests');
        guestValEl.innerHTML = '';
        guestValEl.style.display = 'flex';
        guestValEl.style.flexDirection = 'column';
        guestValEl.style.alignItems = 'flex-end';
        guestValEl.style.flex = '1';
        guestValEl.style.marginLeft = '20px';

        const summaryDiv = document.createElement('div');
        summaryDiv.style.fontWeight = '700';
        summaryDiv.style.color = '#1a1a1a';
        summaryDiv.textContent = gSummary;
        guestValEl.appendChild(summaryDiv);

        const roomsConfigStr = bookingParams.get('rooms_config');
        if (roomsConfigStr) {
            try {
                const rConf = JSON.parse(decodeURIComponent(roomsConfigStr));
                if (Array.isArray(rConf) && rConf.length > 0) {
                    const breakdownCont = document.createElement('div');
                    breakdownCont.style.marginTop = '6px';
                    breakdownCont.style.width = '100%';
                    
                    rConf.forEach((r, i) => {
                        let rt = `${r.adults} adulto${r.adults > 1 ? 's' : ''}`;
                        if (r.children.length > 0) rt += ` · ${r.children.length} niño${r.children.length > 1 ? 's' : ''}`;
                        
                        const row = document.createElement('div');
                        row.style.fontSize = '12px';
                        row.style.color = '#1a1a1a';
                        row.style.marginTop = '4px';
                        row.style.display = 'flex';
                        row.style.justifyContent = 'flex-end';
                        row.style.gap = '8px';
                        row.innerHTML = `<span style="color:#888;">Hab ${i+1}</span><span style="font-weight:600;">${rt}</span>`;
                        breakdownCont.appendChild(row);
                    });
                    guestValEl.appendChild(breakdownCont);
                }
            } catch(e) { console.error('Rooms config parse error', e); }
        }

        document.getElementById('summary-room').textContent = roomName;
        document.getElementById('summary-meal').textContent = mealLabel;
        document.getElementById('summary-price').textContent = `$${Math.round(price)}`;
        
        const nights = Math.ceil((new Date(checkout) - new Date(checkin)) / 86400000) || 1;
        document.getElementById('summary-nights').textContent = `${nights} noche${nights > 1 ? 's' : ''} · IVA incluido`;

        if (refundable) {
            document.getElementById('summary-cancel').textContent = 'Cancelación gratuita según políticas del hotel.';
            document.getElementById('summary-cancel').style.color = '#166534';
            document.getElementById('summary-cancel').previousElementSibling.style.color = '#54991c';
            document.getElementById('summary-cancel').previousElementSibling.textContent = '✓';
        } else {
            document.getElementById('summary-cancel').textContent = 'Reserva no reembolsable.';
            document.getElementById('summary-cancel').style.color = '#991b1b';
            document.getElementById('summary-cancel').previousElementSibling.style.color = '#ef4444';
            document.getElementById('summary-cancel').previousElementSibling.textContent = '✗';
        }

        // Populate Stripe Mock values
        document.getElementById('stripe-hotel-name').textContent = name;
        document.getElementById('stripe-dates').textContent = `${nights} noche${nights > 1 ? 's' : ''} · ${checkin}`;
        document.getElementById('stripe-price').textContent = `$${Math.round(price)}`;
        document.getElementById('btn-pay-text').textContent = `Pagar $${Math.round(price)} USD`;

        // Validate Prebook using existing BookingModule
        if(typeof BookingModule !== 'undefined') {
            BookingModule.prebookValidate(bookingParams.get('book_hash'));
        }
    });

    // ── Interaction Logic ──
    window.toggleCheckbox = function(id) { 
        document.getElementById(id).classList.toggle('checked'); 
    }

    // Connect the submit to the actual Nestay Booking logic
    window.handlePaySubmit = function(e) {
        e.preventDefault();
        
        const t1 = document.getElementById('t1').classList.contains('checked');
        if (!t1) { 
            alert('Por favor acepta los Términos y Condiciones para continuar.'); 
            return; 
        }

        if(typeof BookingModule !== 'undefined') {
            // It will handle the spinner, prebook validation, and API polling
            BookingModule.submitBooking(e, bookingParams);
            
            // Advance progress bar to "Confirmation" visually
            document.getElementById('prog2').className = 'step done';
            document.getElementById('pn2').textContent = '✓';
            document.getElementById('pl2').classList.add('done');
            document.getElementById('prog3').className = 'step active';
            document.getElementById('pn3').textContent = '3';
        }
    }
</script>
@endsection
