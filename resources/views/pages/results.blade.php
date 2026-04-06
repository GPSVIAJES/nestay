@extends('layouts.app')

@section('content')
<div id="view-results">
    <!-- RESULTS HEADER (MINI SEARCH) -->
    <div class="results-header" style="background:var(--wh); border-bottom:1px solid rgba(47,47,47,.08); padding:16px 40px; display:flex; align-items:center; gap:16px; margin-top:0;">
        <div class="results-sbar" style="display:flex; align-items:center; gap:10px; background:var(--cr); border-radius:100px; padding:8px 18px; flex:1; max-width:520px; border:1.5px solid rgba(47,47,47,.08);">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#9A9A9A" stroke-width="2.5" stroke-linecap="round"><circle cx="6.5" cy="6.5" r="4.5"/><line x1="10" y1="10" x2="14.5" y2="14.5"/></svg>
            <input class="rsbar-input" id="rsbar" placeholder="Nuevo destino..." value="{{ request('destination') }}" style="flex:1; border:none; background:transparent; outline:none; font-size:13.5px;">
            <button class="rsbar-btn" onclick="resultsPage.reSearch()" style="background:var(--t); color:#fff; border:none; border-radius:100px; padding:7px 18px; font-size:13px; font-weight:600;">Buscar</button>
        </div>
        <span class="results-meta" style="font-size:13px; color:var(--gm);">
            Mostrando <strong id="res-count">...</strong> alojamientos en <strong id="res-dest">{{ request('destination', 'tu destino') }}</strong>
        </span>
        <div class="results-sort" style="display:flex; align-items:center; gap:8px; margin-left:auto;">
            <span class="sort-lbl" style="font-size:12px; color:var(--gl);">Ordenar:</span>
            <select class="sort-sel" onchange="resultsPage.sortResults(this.value)" style="font-size:13px; background:var(--cr); border:1.5px solid rgba(47,47,47,.1); border-radius:100px; padding:6px 14px;">
                <option value="recommended">Recomendados</option>
                <option value="price_asc">Precio: bajo a alto</option>
                <option value="price_desc">Precio: alto a bajo</option>
                <option value="rating">Mejor valorados</option>
            </select>
        </div>
    </div>

    <div class="results-layout" style="display:grid; grid-template-columns:260px 1fr; gap:24px; padding:28px 40px 60px; background:var(--cr); min-height:70vh;">
        <!-- FILTERS SIDEBAR -->
        <aside class="filters-panel" style="background:var(--wh); border-radius:20px; padding:22px 20px; box-shadow:var(--sh); border:1px solid rgba(47,47,47,.05); height:fit-content; position:sticky; top:90px;">
            <div class="filter-h" style="font-family:'Fraunces',serif; font-size:18px; font-weight:700; margin-bottom:20px;">Filtros</div>

            <div class="filter-group" style="margin-bottom:22px; padding-bottom:22px; border-bottom:1px solid rgba(47,47,47,.07);">
                <span class="filter-label" style="font-size:10px; font-weight:700; color:var(--g); opacity:.4; text-transform:uppercase; letter-spacing:.7px; margin-bottom:12px; display:block;">Precio máximo</span>
                <div class="range-val-cur" id="price-display" style="font-size:13px; font-weight:700; color:var(--t); text-align:center; margin-bottom:8px;">Hasta $ <span id="price-val">500</span></div>
                <input type="range" class="filter-range" min="20" max="1000" value="500" id="price-range" oninput="resultsPage.updatePriceFilter(this.value)" style="width:100%; accent-color:var(--t);">
                <div class="range-vals" style="display:flex; justify-content:space-between; font-size:11px; color:var(--gl);"><span>$20</span><span>$1000+</span></div>
            </div>

            <div class="filter-group" style="margin-bottom:22px; padding-bottom:22px; border-bottom:1px solid rgba(47,47,47,.07);">
                <span class="filter-label" style="font-size:10px; font-weight:700; color:var(--g); opacity:.4; text-transform:uppercase; letter-spacing:.7px; margin-bottom:12px; display:block;">Categoría</span>
                <div class="check-list" style="display:flex; flex-direction:column; gap:8px;">
                    @foreach([5,4,3] as $star)
                    <label class="check-item" style="display:flex; align-items:center; gap:9px; font-size:13.5px; color:var(--gm);">
                        <input type="checkbox" value="{{ $star }}" class="star-filter" onchange="resultsPage.applyFilters()" style="accent-color:var(--t);">
                        <span>{{ $star }} estrellas {{ str_repeat('★', $star) }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <button class="filter-reset" onclick="resultsPage.resetFilters()" style="display:block; width:100%; text-align:center; font-size:12.5px; font-weight:600; color:var(--t); background:var(--tp); border:none; border-radius:100px; padding:9px; cursor:none; margin-top:4px;">Limpiar filtros</button>
        </aside>

        <!-- RESULTS GRID -->
        <main>
            <div class="results-grid" id="hotel-list">
                <!-- Skeletons -->
                @for($i=0;$i<3;$i++)
                <div class="rcard" style="opacity:0.6">
                    <div class="rcard-img">...</div>
                    <div class="rcard-body">
                        <div style="height:20px; background:#eee; width:60%; margin-bottom:10px"></div>
                        <div style="height:15px; background:#eee; width:40%; margin-bottom:10px"></div>
                        <div style="height:30px; background:#eee; width:80%"></div>
                    </div>
                </div>
                @endfor
            </div>
            
            <div id="empty-results" style="display:none; text-align:center; padding:60px 20px;">
                <div style="font-size:48px; margin-bottom:20px;">🏜️</div>
                <h3 style="font-family:'Fraunces',serif; font-size:24px; color:var(--g);">No encontramos nidos aquí</h3>
                <p style="color:var(--gm);">Prueba cambiando los filtros o buscando otro destino.</p>
            </div>
        </main>
    </div>
</div>

<script>
    const resultsPage = {
        allHotels: [],
        filteredHotels: [],
        
        async init() {
            showLoader();
            const params = new URLSearchParams(window.location.search);
            const payload = {
                region_id: parseInt(params.get('region_id')) || 0,
                check_in: params.get('check_in'),
                check_out: params.get('check_out'),
                adults: parseInt(params.get('adults')) || 2,
                rooms: parseInt(params.get('rooms')) || 1
            };

            try {
                const response = await NestayAPI.search(payload);
                this.allHotels = response?.data?.hotels || [];
                this.filteredHotels = [...this.allHotels];
                this.render();
            } catch (e) {
                console.error(e);
            } finally {
                hideLoader();
            }
        },

        render() {
            const list = document.getElementById('hotel-list');
            const countEl = document.getElementById('res-count');
            const emptyEl = document.getElementById('empty-results');

            countEl.textContent = this.filteredHotels.length;

            if (this.filteredHotels.length === 0) {
                list.style.display = 'none';
                emptyEl.style.display = 'block';
                return;
            }

            list.style.display = 'flex';
            list.style.flexDirection = 'column';
            list.style.gap = '14px';
            emptyEl.style.display = 'none';

            list.innerHTML = this.filteredHotels.map(h => this.createCard(h)).join('');
        },

        createCard(h) {
            const rate = h.rates?.[0] || {};
            const img = h.images?.[0] || 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=500';
            const price = Math.round(rate.daily_price || 0);
            const total = Math.round(rate.total_price || 0);
            
            const q = new URLSearchParams(window.location.search);
            q.set('hotel_id', h.id);

            return `
            <div class="rcard" onclick="window.location.href='/hotel/${h.id}?${q.toString()}'">
                <div class="rcard-img" style="background-image:url('${img}'); background-size:cover; background-position:center;">
                    <button class="rcard-fav" onclick="event.stopPropagation(); this.classList.toggle('on');">🤍</button>
                </div>
                <div class="rcard-body">
                    <div class="rcard-loc">📍 ${h.address || h.city}</div>
                    <div class="rcard-name">${h.name}</div>
                    <div class="rcard-type">${h.stars} estrellas · ${h.kind || 'Hotel'}</div>
                    <div class="rcard-amenities">
                        ${(h.amenities || []).slice(0,3).map(a => `<span class="amen-tag">${a}</span>`).join('')}
                    </div>
                </div>
                <div class="rcard-cta">
                    <span class="rcard-badge ${h.rating >= 9 ? 'best' : 'hot'}">${h.rating || 'Nuevo'}</span>
                    <div>
                        <div class="rcard-price-total">$${total}</div>
                        <div class="rcard-per">total estancia</div>
                        <div class="rcard-nightly">$${price}/noche</div>
                    </div>
                    <button class="rcard-book">Ver nido</button>
                </div>
            </div>`;
        },

        applyFilters() {
            const maxPrice = parseFloat(document.getElementById('price-range').value);
            const selectedStars = Array.from(document.querySelectorAll('.star-filter:checked')).map(el => parseInt(el.value));

            this.filteredHotels = this.allHotels.filter(h => {
                const price = h.rates?.[0]?.daily_price || 0;
                const matchesPrice = price <= maxPrice;
                const matchesStars = selectedStars.length === 0 || selectedStars.includes(h.stars);
                return matchesPrice && matchesStars;
            });

            this.render();
        },

        updatePriceFilter(val) {
            document.getElementById('price-val').textContent = val;
            this.applyFilters();
        },

        sortResults(val) {
            if (val === 'price_asc') this.filteredHotels.sort((a,b) => (a.rates?.[0]?.daily_price || 0) - (b.rates?.[0]?.daily_price || 0));
            if (val === 'price_desc') this.filteredHotels.sort((a,b) => (b.rates?.[0]?.daily_price || 0) - (a.rates?.[0]?.daily_price || 0));
            if (val === 'rating') this.filteredHotels.sort((a,b) => (b.rating || 0) - (a.rating || 0));
            this.render();
        },

        reSearch() {
            const dest = document.getElementById('rsbar').value;
            const params = new URLSearchParams(window.location.search);
            params.set('destination', dest);
            window.location.search = params.toString();
        }
    };

    document.addEventListener('DOMContentLoaded', () => resultsPage.init());
</script>
@endsection
