@extends('layouts.app')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Playfair+Display:ital,wght@0,700;1,600&display=swap');

  #view-results {
    font-family: 'DM Sans', sans-serif;
    background: #f5f3f0;
    min-height: 100vh;
    color: #1a1a1a;
  }

  #main-header {
    background: #171717 !important;
    color: #fff !important;
    border-bottom: 1px solid rgba(255,255,255,0.05);
  }
  #main-header a { color: #fff !important; }
  #main-header.sticky {
    background: #171717 !important;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  }

  /* Dark inputs inside the pill form (moved to main header) */
  #main-header input[type=date] { color-scheme: dark; }
  #main-header input::placeholder { color: #555 !important; }

  .type-tag {
    background: #fdf0eb;
    color: #c44a1f;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.3px;
  }

  .results-layout-grid {
    display: flex;
    gap: 0;
    align-items: flex-start;
  }

  .results-sidebar {
    width: 240px;
    flex-shrink: 0;
    background: #fff;
    min-height: calc(100vh - 74px);
    border-right: 0.5px solid #e0ddd8;
    padding: 24px 20px;
    position: sticky;
    top: 80px; /* Only below main site header */
    align-self: flex-start;
  }

  .results-sidebar h3 {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #999;
    margin-bottom: 12px;
    margin-top: 24px;
    font-weight: 600;
  }

  .results-sidebar h3:first-child { margin-top: 0; }

  .price-range-label {
    font-size: 14px;
    color: #e85d2f;
    font-weight: 600;
    margin-bottom: 8px;
  }

  .price-ends {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    color: #999;
    margin-top: 6px;
  }

  .filter-range {
    width: 100%;
    accent-color: #e85d2f;
    height: 4px;
    cursor: pointer;
  }

  .star-opt, .amenity-opt {
    display: flex;
    align-items: center;
    gap: 9px;
    font-size: 13.5px;
    color: #444;
    margin-bottom: 10px;
    cursor: pointer;
  }

  .star-opt input, .amenity-opt input { 
    accent-color: #e85d2f; 
    width: 16px;
    height: 16px;
    cursor: pointer;
  }

  .stars-color { color: #e85d2f; font-size: 11px; }

  .btn-clear-filters {
    width: 100%;
    margin-top: 24px;
    border: 1.5px solid #e85d2f;
    background: none;
    color: #e85d2f;
    border-radius: 10px;
    padding: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
  }

  .btn-clear-filters:hover {
    background: #fdf0eb;
  }

  .results-main-area {
    flex: 1;
    padding: 24px 32px;
  }

  .results-top-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
  }

  .results-count-text {
    font-size: 15px;
    color: #666;
  }

  .results-count-text strong { color: #1a1a1a; }

  .sort-dropdown {
    font-size: 13px;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 8px 14px;
    background: #fff;
    color: #444;
    outline: none;
    cursor: pointer;
  }

  /* Comparison Bar */
  .compare-banner {
    display: none;
    background: #1c1c1c;
    border-radius: 14px;
    padding: 14px 24px;
    margin-bottom: 20px;
    align-items: center;
    justify-content: space-between;
    color: #fff;
    font-size: 13px;
    gap: 16px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
  }

  .compare-banner.visible { display: flex; }

  .compare-slots-list {
    display: flex;
    gap: 10px;
    flex: 1;
    overflow-x: auto;
  }

  .compare-item-slot {
    background: #2e2e2e;
    border-radius: 10px;
    padding: 8px 14px;
    font-size: 12px;
    color: #ccc;
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 140px;
    white-space: nowrap;
    border: 1px solid transparent;
  }

  .compare-item-slot.filled {
    background: #e85d2f;
    color: #fff;
    border-color: #f38a6a;
  }

  .remove-item-x {
    cursor: pointer;
    font-size: 14px;
    font-weight: 700;
    opacity: 0.7;
    margin-left: auto;
  }

  .btn-launch-compare {
    background: #e85d2f;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s;
  }

  .btn-launch-compare:hover:not(:disabled) { transform: translateY(-1px); }

  .btn-launch-compare:disabled {
    background: #444;
    cursor: not-allowed;
    opacity: 0.6;
  }

  /* Hotel Card */
  .hotel-result-card {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    display: flex;
    margin-bottom: 16px;
    border: 1px solid #e0ddd8;
    transition: all 0.25s ease;
    cursor: pointer;
  }

  .hotel-result-card:hover {
    border-color: #e85d2f;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
  }

  .hotel-result-card.selected-for-compare {
    border: 2px solid #e85d2f;
    background: #fffdfc;
  }

  .hotel-card-image {
    width: 260px;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
  }

  .hotel-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .hotel-type-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: rgba(28,28,28,0.85);
    backdrop-filter: blur(4px);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }

  .hotel-content-body {
    flex: 1;
    padding: 22px 24px;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .hotel-address-link {
    font-size: 12px;
    color: #e85d2f;
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: 600;
  }

  .hotel-display-name {
    font-family: 'Playfair Display', serif;
    font-size: 20px;
    color: #1a1a1a;
    line-height: 1.2;
    margin: 2px 0;
  }

  .hotel-meta-info {
    font-size: 13px;
    color: #888;
  }

  .hotel-tags-row {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 4px;
  }

  .feature-pill {
    background: #f5f3f0;
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 11.5px;
    color: #666;
  }

  .hotel-card-footer {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 14px;
    border-top: 0.5px solid #f0ede8;
  }

  .hotel-rating-score {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .rating-number {
    background: #1c1c1c;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    padding: 5px 10px;
    border-radius: 10px;
  }

  .rating-text { font-size: 12px; color: #999; font-weight: 500; }

  .hotel-pricing-box {
    text-align: right;
  }

  .price-total-label {
    font-size: 11px;
    color: #999;
    margin-bottom: 2px;
  }

  .price-night-value {
    font-size: 22px;
    font-weight: 700;
    color: #1a1a1a;
  }

  .price-night-value span { font-size: 12px; color: #999; font-weight: 400; }

  .hotel-card-buttons {
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 22px 20px 22px 0;
    gap: 10px;
    flex-shrink: 0;
  }

  .btn-action-view {
    background: #e85d2f;
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    text-align: center;
  }

  .btn-action-compare {
    background: #fff;
    border: 1.5px solid #ddd;
    border-radius: 12px;
    padding: 9px 20px;
    font-size: 13.5px;
    color: #555;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    white-space: nowrap;
    transition: all 0.2s;
  }

  .btn-action-compare.active {
    border-color: #e85d2f;
    color: #e85d2f;
    background: #fffdfc;
  }

  /* Modal */
  .compare-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  .compare-modal-overlay.active { display: flex; }

  .compare-modal {
    background: #fff;
    border-radius: 24px;
    width: 800px;
    max-width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    padding: 32px;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
  }

  .modal-close-x {
    position: absolute;
    top: 20px;
    right: 24px;
    background: #f5f3f0;
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #555;
    transition: background 0.2s;
  }

  .modal-close-x:hover { background: #eee; }

  .compare-modal-h1 {
    font-family: 'Playfair Display', serif;
    font-size: 26px;
    margin-bottom: 6px;
    color: #1a1a1a;
  }

  .compare-modal-p {
    font-size: 14px;
    color: #888;
    margin-bottom: 28px;
  }

  .compare-table {
    width: 100%;
    border-collapse: collapse;
  }

  .compare-table th {
    text-align: center;
    font-family: 'Playfair Display', serif;
    font-size: 16px;
    padding: 0 16px 18px;
    border-bottom: 2px solid #f0ede8;
    font-weight: 700;
    vertical-align: top;
  }

  .hotel-th-stars {
    display: block;
    font-family: 'DM Sans', sans-serif;
    font-size: 12px;
    color: #e85d2f;
    margin-top: 4px;
  }

  .compare-table td {
    padding: 12px 16px;
    text-align: center;
    font-size: 14px;
    border-bottom: 1px solid #f0ede8;
    color: #444;
  }

  .compare-table tr:last-child td { border-bottom: none; }

  .compare-table td:first-child {
    text-align: left;
    color: #999;
    font-size: 12px;
    font-weight: 600;
    width: 150px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .check-icon-v { color: #22a87a; font-size: 18px; font-weight: 700; }
  .cross-icon-x { color: #ddd; font-size: 18px; }

  .table-price-val {
    font-size: 18px;
    font-weight: 700;
    color: #1a1a1a;
  }

  .table-score-badge {
    background: #1c1c1c;
    color: #fff;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    display: inline-block;
  }

  @keyframes spin { to { transform: rotate(360deg); } }

  .main-header {
    background: #171717;
    color: #fff;
  }

  .spinner {
    width: 48px;
    height: 48px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #e85d2f;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }
</style>

@section('header_search')
    <form action="{{ route('search') }}" method="GET" onsubmit="return SearchMix.validateSearch(event)" style="display:flex; align-items:center; gap:0; background:#1f1f1f; border-radius:50px; padding:6px 6px 6px 20px; border:1px solid rgba(255,255,255,0.06); width:100%;">

        {{-- Destino --}}
        <div style="flex:1.7; min-width:0; position:relative; padding-right:16px; border-right:1px solid rgba(255,255,255,0.1);">
            <div style="font-size:9px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#888; margin-bottom:2px;">Destino</div>
            <input type="text" name="destination" id="dest" autocomplete="off" required
                placeholder="¿Dónde vas?"
                value="{{ request('destination') }}"
                oninput="SearchMix.onDestInput(this.value)"
                style="width:100%; background:transparent; border:none; outline:none; color:#fff; font-size:13px; font-weight:500; placeholder-color:#666;">
            <input type="hidden" name="region_id" id="region-id-input" value="{{ request('region_id') }}">
            <div id="dest-suggestions" style="display:none; position:absolute; top:110%; left:-20px; right:0; background:#fff; border-radius:14px; box-shadow:0 12px 40px rgba(0,0,0,0.2); z-index:9999; overflow:hidden; border:1px solid #eee; color: #1a1a1a;"></div>
        </div>

        {{-- Entrada --}}
        <div style="flex:1; min-width:0; padding:0 16px; border-right:1px solid rgba(255,255,255,0.1);">
            <div style="font-size:9px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#888; margin-bottom:2px;">Entrada</div>
            <input type="date" name="check_in" id="cin" required
                value="{{ request('check_in') }}"
                min="{{ date('Y-m-d') }}"
                onchange="SearchMix.onCheckinChange(this.value)"
                style="background:transparent; border:none; outline:none; color:#fff; font-size:13px; font-weight:500; width:100%;">
        </div>

        {{-- Salida --}}
        <div style="flex:1; min-width:0; padding:0 16px; border-right:1px solid rgba(255,255,255,0.1);">
            <div style="font-size:9px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#888; margin-bottom:2px;">Salida</div>
            <input type="date" name="check_out" id="cout" required
                value="{{ request('check_out') }}"
                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                style="background:transparent; border:none; outline:none; color:#fff; font-size:13px; font-weight:500; width:100%;">
        </div>

        {{-- Huéspedes --}}
        <div style="flex:1; min-width:0; padding:0 16px; position:relative;">
            <div style="font-size:9px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#888; margin-bottom:2px;">Huéspedes</div>
            <div onclick="SearchMix.toggleGuest()" style="color:#fff; font-size:13px; font-weight:500; cursor:pointer; white-space:nowrap;" id="guest-summary">
                {{ request('adults', 2) }} adultos · {{ request('rooms', 1) }} hab
            </div>
            <input type="hidden" name="adults" id="adults-input" value="{{ request('adults', 2) }}">
            <input type="hidden" name="rooms" id="rooms-input" value="{{ request('rooms', 1) }}">
            <input type="hidden" name="children" id="children-input" value="{{ request('children', 0) }}">
            <div class="guest-hub-panel" id="guest-hub-panel" style="top:110%; right:0; left:auto; width:290px; padding:20px; background:#fff; border-radius:18px; border:1px solid #eee; box-shadow:0 16px 48px rgba(0,0,0,0.15); color: #1a1a1a;">
                <div id="rooms-container"></div>
                <button type="button" onclick="SearchMix.addRoom()" style="width:100%; background:none; border:1.5px dashed #ddd; border-radius:10px; padding:8px; font-size:12px; color:#999; cursor:pointer; margin-top:10px;">+ Agregar habitación</button>
                <button type="button" onclick="SearchMix.toggleGuest()" style="width:100%; background:#e85d2f; color:#fff; border:none; border-radius:10px; padding:10px; font-size:13px; font-weight:600; cursor:pointer; margin-top:10px;">Listo</button>
            </div>
        </div>

        {{-- Botón buscar --}}
        <button type="submit" style="background:#e85d2f; border:none; border-radius:50px; color:#fff; font-size:13px; font-weight:700; padding:12px 24px; cursor:pointer; white-space:nowrap; flex-shrink:0; display:flex; align-items:center; gap:8px; transition:transform .2s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            Buscar
        </button>
    </form>
@endsection

<div id="view-results" style="padding-top: 80px;">
    <div class="results-layout-grid">
        <!-- Sidebar Filters -->
        <aside class="results-sidebar">
            <h3>Precio máximo</h3>
            <div class="price-range-label" id="price-label-sidebar">Hasta $1000</div>
            <input type="range" min="20" max="1000" value="1000" id="price-slider-sidebar" oninput="resultsPage.updatePriceFilter(this.value)" class="filter-range">
            <div class="price-ends"><span>$20</span><span>$1000+</span></div>

            <h3>Categoría</h3>
            <div id="stars-filters">
                <label class="star-opt"><input type="checkbox" value="5" class="star-filter" onchange="resultsPage.applyFilters()"> <span class="stars-color">★★★★★</span> 5 estrellas</label>
                <label class="star-opt"><input type="checkbox" value="4" class="star-filter" onchange="resultsPage.applyFilters()"> <span class="stars-color">★★★★</span> 4 estrellas</label>
                <label class="star-opt"><input type="checkbox" value="3" class="star-filter" onchange="resultsPage.applyFilters()"> <span class="stars-color">★★★</span> 3 estrellas</label>
            </div>

            <h3>Servicios</h3>
            <div id="amenities-filters">
                <label class="amenity-opt"><input type="checkbox" value="Wifi" onchange="resultsPage.applyFilters()"> Wifi gratuito</label>
                <label class="amenity-opt"><input type="checkbox" value="Piscina" onchange="resultsPage.applyFilters()"> Piscina</label>
                <label class="amenity-opt"><input type="checkbox" value="Spa" onchange="resultsPage.applyFilters()"> Spa</label>
                <label class="amenity-opt"><input type="checkbox" value="Gimnasio" onchange="resultsPage.applyFilters()"> Gimnasio</label>
                <label class="amenity-opt"><input type="checkbox" value="Parking" onchange="resultsPage.applyFilters()"> Parking</label>
            </div>

            <button class="btn-clear-filters" onclick="resultsPage.resetFilters()">Limpiar filtros</button>
        </aside>

        <!-- Main Content -->
        <main class="results-main-area">
            <div class="results-top-actions">
                <div class="results-count-text"><strong><span id="res-count-main">...</span> alojamientos</strong> encontrados</div>
                <select class="sort-dropdown" onchange="resultsPage.sortResults(this.value)">
                    <option value="recommended">Ordenar: Recomendados</option>
                    <option value="price_asc">Precio: menor a mayor</option>
                    <option value="price_desc">Precio: mayor a menor</option>
                    <option value="rating">Mejor valoración</option>
                </select>
            </div>

            <!-- Compare Banner -->
            <div class="compare-banner" id="compareBanner">
                <div style="font-weight:600;font-size:12px;color:#aaa;text-transform:uppercase;letter-spacing:0.5px;">Comparando</div>
                <div class="compare-slots-list" id="compareSlots">
                    <!-- Slots will be injected here -->
                </div>
                <button class="btn-launch-compare" id="btnLaunchCompare" onclick="resultsPage.openCompareModal()" disabled>Ver comparación</button>
            </div>

            <!-- Hotel List -->
            <div id="hotel-list-container">
                <!-- Loader shown initially -->
                <div id="initial-results-loader" style="padding:100px 0; text-align:center;">
                    <div class="spinner" style="margin:0 auto 16px;"></div>
                    <p style="color:#888;">Buscando los mejores nidos...</p>
                </div>
            </div>

            <div id="empty-results-view" style="display:none; text-align:center; padding:80px 20px;">
                <div style="font-size:48px; margin-bottom:20px;">🏜️</div>
                <h3 style="font-family:'Playfair Display',serif; font-size:24px; color:#1a1a1a;">No encontramos nidos aquí</h3>
                <p style="color:#888; max-width:300px; margin:0 auto;">Prueba cambiando los filtros o buscando otro destino.</p>
            </div>
        </main>
    </div>
</div>

<!-- Comparison Modal -->
<div class="compare-modal-overlay" id="compareModalOverlay" onclick="if(event.target===this)resultsPage.closeCompareModal()">
    <div class="compare-modal">
        <button class="modal-close-x" onclick="resultsPage.closeCompareModal()">✕</button>
        <h2 class="compare-modal-h1">Comparación de alojamientos</h2>
        <p class="compare-modal-p">Compara características clave para tomar la mejor decisión</p>
        <div id="modal-table-container">
            <!-- Table injected here -->
        </div>
        <div style="display:flex; justify-content:flex-end; margin-top:32px;">
            <button class="btn-action-view" onclick="resultsPage.closeCompareModal()" style="background:#f5f3f0; color:#555;">Cerrar ventana</button>
        </div>
    </div>
</div>

<script>
    // ── SEARCH MIX MODULE (Adapted for results page) ──────────────────
    const SearchMix = {
        rooms: [{ adults: parseInt(new URLSearchParams(window.location.search).get('adults')) || 2, children: [] }],
        
        toggleGuest() {
            const panel = document.getElementById('guest-hub-panel');
            panel.classList.toggle('active');
            if (panel.classList.contains('active')) this.renderRooms();
        },

        adjustRoom(idx, type, delta) {
            const r = this.rooms[idx-1];
            if (!r) return;
            if (type === 'adults') {
                r.adults = Math.max(1, Math.min(6, r.adults + delta));
            }
            this.renderRooms();
            this.syncInputs();
        },

        addChild(roomIdx) {
            const r = this.rooms[roomIdx-1];
            if (r.children.length >= 4) return;
            r.children.push(8);
            this.renderRooms();
            this.syncInputs();
        },

        removeChild(roomIdx, childIdx) {
            this.rooms[roomIdx-1].children.splice(childIdx, 1);
            this.renderRooms();
            this.syncInputs();
        },

        setChildAge(roomIdx, childIdx, age) {
            this.rooms[roomIdx-1].children[childIdx] = parseInt(age);
            this.syncInputs();
        },

        addRoom() {
            if (this.rooms.length >= 4) return;
            this.rooms.push({ adults: 2, children: [] });
            this.renderRooms();
            this.syncInputs();
        },

        renderRooms() {
            const container = document.getElementById('rooms-container');
            if (!container) return;
            container.innerHTML = '';
            this.rooms.forEach((room, i) => {
                const idx = i + 1;
                const block = document.createElement('div');
                block.className = 'room-block';
                block.innerHTML = `
                    <div class="room-header" style="font-size:12px; margin-bottom:10px; font-weight:700; color:var(--t); display:flex; align-items:center; gap:6px;">
                        <span>Habitación ${idx}</span>
                        ${idx > 1 ? `<span onclick="SearchMix.removeRoom(${idx})" style="margin-left:auto; cursor:pointer; opacity:0.5; color:#1a1a1a;">✕</span>` : ''}
                    </div>
                    <div class="hub-row" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; color:#1a1a1a;">
                        <span style="font-size:13px; font-weight:500;">Adultos</span>
                        <div class="hub-ctrl" style="display:flex; align-items:center; gap:10px;">
                            <button type="button" class="hub-btn" onclick="SearchMix.adjustRoom(${idx},'adults',-1)" style="width:28px; height:28px; border-radius:50%; border:1px solid #ddd; background:#fff; color:#1a1a1a; display:flex; align-items:center; justify-content:center; cursor:pointer;">−</button>
                            <span style="font-weight:700; min-width:14px; text-align:center; font-size:14px;">${room.adults}</span>
                            <button type="button" class="hub-btn" onclick="SearchMix.adjustRoom(${idx},'adults',1)" style="width:28px; height:28px; border-radius:50%; border:1px solid #ddd; background:#fff; color:#1a1a1a; display:flex; align-items:center; justify-content:center; cursor:pointer;">+</button>
                        </div>
                    </div>
                    <div id="room-${idx}-children"></div>
                    <button type="button" class="add-child-btn" onclick="SearchMix.addChild(${idx})" style="background:none; border:none; color:var(--t); font-size:11px; font-weight:700; cursor:pointer; padding:4px 0;">+ Añadir niño</button>
                `;
                container.appendChild(block);
                this.renderChildren(idx);
            });
        },

        renderChildren(roomIdx) {
            const r = this.rooms[roomIdx-1];
            const cont = document.getElementById(`room-${roomIdx}-children`);
            cont.innerHTML = r.children.map((age, cIdx) => `
                <div class="hub-row" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; color:#1a1a1a;">
                    <span style="font-size:12px; font-weight:500;">Niño ${cIdx+1}</span>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <select onchange="SearchMix.setChildAge(${roomIdx}, ${cIdx}, this.value)" style="font-size:12px; border-radius:8px; border:1px solid #ddd; padding:4px 8px; background:#f9f9f9; color:#1a1a1a; cursor:pointer;">
                            ${[...Array(18).keys()].map(a => `<option value="${a}" ${a==age?'selected':''}>${a} años</option>`).join('')}
                        </select>
                        <span onclick="SearchMix.removeChild(${roomIdx}, ${cIdx})" style="cursor:pointer; opacity:0.4; font-size:14px; color:#1a1a1a; padding:4px;">✕</span>
                    </div>
                </div>
            `).join('');
        },

        removeRoom(idx) {
            this.rooms.splice(idx-1, 1);
            this.renderRooms();
            this.syncInputs();
        },

        syncInputs() {
            const adults = this.rooms.reduce((s, r) => s + r.adults, 0);
            const rooms = this.rooms.length;
            document.getElementById('adults-input').value = adults;
            document.getElementById('rooms-input').value = rooms;
            document.getElementById('guest-summary').textContent = `${adults} ad · ${rooms} hab`;
        },

        _destTimer: null,
        onDestInput(val) {
            const box = document.getElementById('dest-suggestions');
            clearTimeout(this._destTimer);
            if (val.length < 3) { box.style.display = 'none'; return; }
            this._destTimer = setTimeout(() => this.fetchSuggestions(val), 300);
        },

        async fetchSuggestions(query) {
            const box = document.getElementById('dest-suggestions');
            try {
                const res = await fetch('/api/suggest', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                    },
                    body: JSON.stringify({ q: query, language: 'es' })
                });
                const data = await res.json();
                const items = data?.data || [];
                if (!items.length) { box.style.display = 'none'; return; }
                box.innerHTML = items.slice(0, 5).map(item => `
                    <div style="padding:10px 14px; cursor:pointer; font-size:13px; border-bottom:1px solid #f0ede8;" 
                         onclick="SearchMix.selectDest(${item.id}, '${item.name.replace(/'/g, "\\'")}')">
                        ${item.type==='hotel'?'🏨':'📍'} <strong>${item.name}</strong> <small style="color:#999">${item.country_name || ''}</small>
                    </div>
                `).join('');
                box.style.display = 'block';
            } catch(e) { console.error(e); }
        },

        selectDest(id, name) {
            document.getElementById('dest').value = name;
            document.getElementById('region-id-input').value = id;
            document.getElementById('dest-suggestions').style.display = 'none';
        },

        onCheckinChange(val) {
            const cout = document.getElementById('cout');
            if (cout) cout.min = val;
        },

        validateSearch(e) {
            if (!document.getElementById('region-id-input').value) {
                alert('Por favor selecciona un destino de la lista');
                e.preventDefault();
                return false;
            }
            return true;
        }
    };

    const resultsPage = {
        allHotels: [],
        filteredHotels: [],
        compareSet: new Set(),

        // ── Fallback local data ─────────────────────
        _fallbackHotels: [
            { id:'hotel_madrid_01', name:'Gran Hotel Melia Madrid',       stars:5, rating:8.9, address:'Calle de Recoletos 4, Madrid', city:'Madrid', kind:'Hotel',
              images:['https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800'],
              amenities:['WiFi','Piscina','Spa','Restaurante','Gimnasio','Aire acondicionado'], rates:[{daily_price:189,total_price:567,currency:'USD'}] },
            { id:'hotel_madrid_02', name:'Hotel NH Collection Suecia',    stars:4, rating:8.4, address:'Marqués de Casa Riera 4, Madrid', city:'Madrid', kind:'Hotel',
              images:['https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800'],
              amenities:['WiFi','Restaurante','Bar','Gimnasio','Parking'], rates:[{daily_price:125,total_price:375,currency:'USD'}] },
            { id:'hotel_madrid_03', name:'Barceló Torre de Madrid',       stars:4, rating:8.7, address:'Plaza de España 18, Madrid', city:'Madrid', kind:'Hotel',
              images:['https://images.unsplash.com/photo-1582719508461-905c673771fd?w=800'],
              amenities:['WiFi','Piscina','Spa','Rooftop','Bar'], rates:[{daily_price:145,total_price:435,currency:'USD'}] },
            { id:'hotel_madrid_04', name:'Ibis Madrid Centro Las Ventas', stars:2, rating:7.6, address:'Calle Alcalá 276, Madrid', city:'Madrid', kind:'Hotel',
              images:['https://images.unsplash.com/photo-1576354302919-96748cb8299e?w=800'],
              amenities:['WiFi','Desayuno','Parking','Aire acondicionado'], rates:[{daily_price:65,total_price:195,currency:'USD'}] },
            { id:'hotel_madrid_05', name:'Rosewood Villa Magna Madrid',   stars:5, rating:9.2, address:'Paseo de la Castellana 22, Madrid', city:'Madrid', kind:'Hotel',
              images:['https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800'],
              amenities:['WiFi','Piscina','Spa','Concierge','Butler','Gimnasio','Restaurante'], rates:[{daily_price:480,total_price:1440,currency:'USD'}] },
        ],

        async init() {
            const params = new URLSearchParams(window.location.search);
            const payload = {
                region_id: parseInt(params.get('region_id')) || 0,
                checkin:   params.get('check_in'),
                checkout:  params.get('check_out'),
                adults:    parseInt(params.get('adults')) || 2,
                rooms:     parseInt(params.get('rooms')) || 1
            };

            const destName = params.get('destination') || '';
            const destTop = document.getElementById('res-dest-top');
            if (destTop && destName) destTop.textContent = destName;

            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 8000);

                const response = await fetch('/api/search-hotels', {
                    method: 'POST',
                    signal: controller.signal,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify(payload),
                });
                clearTimeout(timeoutId);

                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                const data = await response.json();
                const hotels = data?.data?.hotels || [];
                this.allHotels = hotels.length > 0 ? hotels : this._fallbackHotels;
            } catch (e) {
                console.warn('[Nestay] API Error, using fallback:', e.message);
                this.allHotels = this._fallbackHotels;
            } finally {
                this.filteredHotels = [...this.allHotels];
                this.render();
                document.getElementById('initial-results-loader').style.display = 'none';
            }
        },

        render() {
            const container = document.getElementById('hotel-list-container');
            const countMain = document.getElementById('res-count-main');
            const emptyView = document.getElementById('empty-results-view');
            const loader = document.getElementById('initial-results-loader');

            // Always hide loader first
            if (loader) loader.style.display = 'none';

            if (countMain) countMain.textContent = this.filteredHotels.length;

            if (this.filteredHotels.length === 0) {
                if (container) container.innerHTML = '';
                if (emptyView) emptyView.style.display = 'block';
                return;
            }

            if (emptyView) emptyView.style.display = 'none';
            if (container) {
                container.style.display = 'block';
                container.innerHTML = this.filteredHotels.map(h => this.createCardHTML(h)).join('');
            }
        },

        createCardHTML(h) {
            const isComparing = this.compareSet.has(h.id);
            const rate = h.rates?.[0] || {};
            const img = h.images?.[0] || 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=500';
            const price = Math.round(rate.daily_price || 0);
            const total = Math.round(rate.total_price || 0);
            const ratingText = h.rating >= 9 ? 'Excepcional' : (h.rating >= 8.5 ? 'Excelente' : (h.rating >= 8 ? 'Muy bueno' : 'Bueno'));

            const q = new URLSearchParams(window.location.search);
            q.set('hotel_id', h.id);
            q.set('hotel_name', h.name || '');
            q.set('hotel_address', h.address || h.city || '');
            q.set('hotel_stars', h.stars || 0);
            const detailUrl = `/hotel/${h.id}?${q.toString()}`;

            return `
            <div class="hotel-result-card ${isComparing ? 'selected-for-compare' : ''}" onclick="window.location.href='${detailUrl}'">
                <div class="hotel-card-image">
                    <img src="${img}" alt="${h.name}">
                    <div class="hotel-type-badge">${h.stars} estrellas</div>
                </div>
                <div class="hotel-content-body">
                    <div class="hotel-address-link">
                        <svg width="10" height="10" viewBox="0 0 14 14" fill="none"><path d="M7 1C4.2 1 2 3.2 2 6c0 3.5 5 8 5 8s5-4.5 5-8c0-2.8-2.2-5-5-5zm0 6.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3z" fill="currentColor"/></svg>
                        ${h.address || h.city}
                    </div>
                    <div class="hotel-display-name">${h.name}</div>
                    <div class="hotel-meta-info">${h.kind || 'Hotel'} · ${h.city}</div>
                    <div class="hotel-tags-row">
                        ${(h.amenities || []).slice(0,4).map(a => `<span class="feature-pill">${a}</span>`).join('')}
                    </div>
                    <div class="hotel-card-footer">
                        <div class="hotel-rating-score">
                            <span class="rating-number">${h.rating || 'N/A'}</span>
                            <span class="rating-text">${h.rating ? ratingText : 'Nuevo'}</span>
                        </div>
                        <div class="hotel-pricing-box">
                            <div class="price-total-label">Estancia total: $${total}</div>
                            <div class="price-night-value">$${price} <span>/noche</span></div>
                        </div>
                    </div>
                </div>
                <div class="hotel-card-buttons">
                    <button class="btn-action-view" onclick="event.stopPropagation(); window.location.href='${detailUrl}'">Ver nido</button>
                    <button class="btn-action-compare ${isComparing ? 'active' : ''}" onclick="event.stopPropagation(); resultsPage.toggleCompare('${h.id}')">
                        <span>${isComparing ? '✓' : '+'}</span>
                        ${isComparing ? 'Comparando' : 'Comparar'}
                    </button>
                </div>
            </div>`;
        },

        toggleCompare(id) {
            if (this.compareSet.has(id)) {
                this.compareSet.delete(id);
            } else {
                if (this.compareSet.size >= 3) {
                    alert('Puedes comparar un máximo de 3 alojamientos.');
                    return;
                }
                this.compareSet.add(id);
            }
            this.render();
            this.updateCompareBanner();
        },

        updateCompareBanner() {
            const banner = document.getElementById('compareBanner');
            const slots = document.getElementById('compareSlots');
            const btn = document.getElementById('btnLaunchCompare');

            if (this.compareSet.size === 0) {
                banner.classList.remove('visible');
                return;
            }

            banner.classList.add('visible');
            const selectedIds = Array.from(this.compareSet);
            slots.innerHTML = '';

            [0, 1, 2].forEach(i => {
                const id = selectedIds[i];
                if (id) {
                    const h = this.allHotels.find(x => x.id == id);
                    const name = h ? h.name.split(' ').slice(0,2).join(' ') : 'Hotel...';
                    slots.innerHTML += `
                        <div class="compare-item-slot filled">
                            <span>${name}</span>
                            <span class="remove-item-x" onclick="resultsPage.toggleCompare('${id}')">✕</span>
                        </div>`;
                } else {
                    slots.innerHTML += `<div class="compare-item-slot">Añadir...</div>`;
                }
            });

            btn.disabled = this.compareSet.size < 2;
        },

        openCompareModal() {
            const hotels = Array.from(this.compareSet).map(id => this.allHotels.find(h => h.id == id));
            const features = [
                { label: 'Precio por noche', key: 'price' },
                { label: 'Valoración', key: 'rating' },
                { label: 'Wifi', key: 'WiFi' },
                { label: 'Piscina', key: 'Piscina' },
                { label: 'Spa', key: 'Spa' },
                { label: 'Gimnasio', key: 'Gimnasio' },
                { label: 'Parking', key: 'Parking' },
                { label: 'Restaurante', key: 'Restaurante' },
                { label: 'Bar', key: 'Bar' },
                { label: 'A/C', key: 'Aire acondicionado' },
            ];

            let html = `<table class="compare-table"><thead><tr><th> Característica </th>`;
            hotels.forEach(h => {
                html += `<th>${h.name.split(' ').slice(0,3).join(' ')}<span class="hotel-th-stars">${'★'.repeat(h.stars)}</span></th>`;
            });
            html += `</tr></thead><tbody>`;

            features.forEach(f => {
                html += `<tr><td>${f.label}</td>`;
                hotels.forEach(h => {
                    if (f.key === 'price') {
                        html += `<td><span class="table-price-val">$${Math.round(h.rates?.[0]?.daily_price || 0)}</span></td>`;
                    } else if (f.key === 'rating') {
                        html += `<td><span class="table-score-badge">${h.rating || 'N/A'}</span></td>`;
                    } else {
                        const has = (h.amenities || []).some(a => a.toLowerCase().includes(f.key.toLowerCase()));
                        html += `<td><span class="${has ? 'check-icon-v' : 'cross-icon-x'}">${has ? '✓' : '✕'}</span></td>`;
                    }
                });
                html += `</tr>`;
            });

            html += `</tbody></table>`;
            document.getElementById('modal-table-container').innerHTML = html;
            document.getElementById('compareModalOverlay').classList.add('active');
        },

        closeCompareModal() {
            document.getElementById('compareModalOverlay').classList.remove('active');
        },

        applyFilters() {
            const maxPrice = parseFloat(document.getElementById('price-slider-sidebar').value);
            const selectedStars = Array.from(document.querySelectorAll('.star-filter:checked')).map(el => parseInt(el.value));
            const selectedAmens = Array.from(document.querySelectorAll('#amenities-filters input:checked')).map(el => el.value.toLowerCase());

            this.filteredHotels = this.allHotels.filter(h => {
                const price = h.rates?.[0]?.daily_price || 0;
                const matchesPrice = price <= maxPrice;
                const matchesStars = selectedStars.length === 0 || selectedStars.includes(h.stars);
                const matchesAmens = selectedAmens.length === 0 || selectedAmens.every(sa => 
                    (h.amenities || []).some(a => a.toLowerCase().includes(sa))
                );
                return matchesPrice && matchesStars && matchesAmens;
            });
            this.render();
        },

        updatePriceFilter(val) {
            document.getElementById('price-label-sidebar').textContent = `Hasta $${val}`;
            this.applyFilters();
        },

        sortResults(val) {
            if (val === 'price_asc') this.filteredHotels.sort((a,b) => (a.rates?.[0]?.daily_price || 0) - (b.rates?.[0]?.daily_price || 0));
            else if (val === 'price_desc') this.filteredHotels.sort((a,b) => (b.rates?.[0]?.daily_price || 0) - (a.rates?.[0]?.daily_price || 0));
            else if (val === 'rating') this.filteredHotels.sort((a,b) => (b.rating || 0) - (a.rating || 0));
            this.render();
        },

        resetFilters() {
            document.getElementById('price-slider-sidebar').value = 1000;
            document.getElementById('price-label-sidebar').textContent = 'Hasta $1000';
            document.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);
            this.filteredHotels = [...this.allHotels];
            this.render();
        }
    };

    document.addEventListener('DOMContentLoaded', () => resultsPage.init());
</script>
@endsection
