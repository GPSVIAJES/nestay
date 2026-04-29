@extends('layouts.app')

@section('header_search')
    <form action="{{ route('search') }}" method="GET" onsubmit="return SearchMix.validateSearch(event)" style="display:flex; align-items:center; gap:0; background:#1f1f1f; border-radius:50px; padding:6px 6px 6px 20px; border:1px solid rgba(255,255,255,0.06); width:100%; max-width: 900px; margin: 0 auto;">
        {{-- Destino --}}
        <div style="flex:1.7; min-width:0; position:relative; padding-right:16px; border-right:1px solid rgba(255,255,255,0.1);">
            <div style="font-size:9px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#888; margin-bottom:2px;">Destino</div>
            <input type="text" name="destination" id="dest" autocomplete="off" required placeholder="¿A dónde vas?" value="{{ request('destination') }}" oninput="SearchMix.onDestInput(this.value)"
                   style="width:100%; background:transparent; border:none; outline:none; color:#fff; font-size:13px; font-weight:500;">
            <input type="hidden" name="region_id" id="region-id-input" value="{{ request('region_id') }}">
            <div id="dest-suggestions" style="display:none; position:absolute; top:110%; left:-20px; right:0; background:#fff; border-radius:14px; box-shadow:0 12px 40px rgba(0,0,0,0.2); z-index:9999; overflow:hidden; border:1px solid #eee; color: #1a1a1a;"></div>
        </div>

        {{-- Entrada --}}
        <div style="flex:1; min-width:0; padding:0 16px; border-right:1px solid rgba(255,255,255,0.1);">
            <div style="font-size:9px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#888; margin-bottom:2px;">Entrada</div>
            <input type="date" name="check_in" id="cin" required value="{{ request('check_in') }}" min="{{ date('Y-m-d') }}" onchange="SearchMix.onCheckinChange(this.value)"
                   style="background:transparent; border:none; outline:none; color:#fff; font-size:13px; font-weight:500; width:100%; color-scheme: dark;">
        </div>

        {{-- Salida --}}
        <div style="flex:1; min-width:0; padding:0 16px; border-right:1px solid rgba(255,255,255,0.1);">
            <div style="font-size:9px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#888; margin-bottom:2px;">Salida</div>
            <input type="date" name="check_out" id="cout" required value="{{ request('check_out') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                   style="background:transparent; border:none; outline:none; color:#fff; font-size:13px; font-weight:500; width:100%; color-scheme: dark;">
        </div>

        {{-- Huéspedes --}}
        <div style="flex:1; min-width:0; padding:0 16px; position:relative;">
            <div style="font-size:9px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#888; margin-bottom:2px;">Huéspedes</div>
            @php
                $adults = request('adults', 2);
                $children = request('children', 0);
                $rooms = request('rooms', 1);
                $total = $adults + $children;
                $label = $children > 0 ? ($total > 1 ? 'huéspedes' : 'huésped') : ($adults > 1 ? 'adultos' : 'adulto');
            @endphp
            <div onclick="SearchMix.toggleGuest()" style="color:#fff; font-size:13px; font-weight:500; cursor:pointer; white-space:nowrap;" id="guest-summary">
                {{ $total }} {{ $label }} · {{ $rooms }} hab
            </div>
            <input type="hidden" name="adults" id="adults-input" value="{{ $adults }}">
            <input type="hidden" name="rooms" id="rooms-input" value="{{ $rooms }}">
            <input type="hidden" name="children" id="children-input" value="{{ $children }}">
            <input type="hidden" name="rooms_config" id="rooms-config-input" value="{{ request('rooms_config') }}">
            
            <div class="guest-hub-panel" id="guest-hub-panel" style="top:110%; right:0; left:auto; width:290px; padding:20px; background:#fff; border-radius:18px; border:1px solid #eee; box-shadow:0 16px 48px rgba(0,0,0,0.15); color: #1a1a1a;">
                <div id="rooms-container"></div>
                <button type="button" onclick="SearchMix.addRoom()" style="width:100%; background:none; border:1.5px dashed #ddd; border-radius:10px; padding:8px; font-size:12px; color:#999; cursor:pointer; margin-top:10px;">+ Agregar habitación</button>
                <button type="button" onclick="SearchMix.toggleGuest()" style="width:100%; background:var(--t); color:#fff; border:none; border-radius:10px; padding:10px; font-size:13px; font-weight:600; cursor:pointer; margin-top:10px;">Listo</button>
            </div>
        </div>

        {{-- Botón buscar --}}
        <button type="submit" style="margin-left: 12px; background:var(--t); border:none; border-radius:50px; color:#fff; font-size:13px; font-weight:700; padding:12px 24px; cursor:pointer; white-space:nowrap; flex-shrink:0; display:flex; align-items:center; gap:8px; transition:transform .2s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            Buscar
        </button>
    </form>
@endsection

@section('content')
<style>
    #main-header {
        background: #171717 !important;
        color: #fff !important;
        border-bottom: 1px solid rgba(255,255,255,0.05) !important;
    }
    #main-header a { color: #fff !important; }
    #main-header .btn-ghost { color: #fff !important; opacity: 0.8; }
    #main-header.sticky { background: #171717 !important; }
</style>
<div id="dashboard-page" style="background:var(--cr); min-height:100vh; padding:120px 20px 80px;">
    <div style="max-width:1200px; margin:0 auto; display:grid; grid-template-columns:280px 1fr; gap:40px; align-items:start;">
        
        <!-- SIDEBAR -->
        <aside class="reveal" style="background:var(--wh); border-radius:32px; padding:40px 32px; border:1px solid rgba(0,0,0,0.05); box-shadow:var(--sh); position:sticky; top:110px;">
            <div style="text-align:center; margin-bottom:40px;">
                <div style="width:72px; height:72px; background:var(--t); color:#fff; border-radius:50%; margin:0 auto 16px; display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:800; box-shadow: 0 8px 20px rgba(238, 108, 77, 0.2);">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div style="font-size:20px; font-weight:800; color:var(--v); letter-spacing:-0.5px;">{{ Auth::user()->name }}</div>
                <div style="font-size:12px; color:var(--gm); opacity:0.6; margin-top:4px;">Miembro desde {{ Auth::user()->created_at->format('M Y') }}</div>
            </div>

            <nav style="display:flex; flex-direction:column; gap:12px;">
                <a href="{{ route('dashboard') }}" 
                   style="display:flex; align-items:center; gap:14px; padding:14px 20px; border-radius:18px; font-size:14px; font-weight:700; transition:all .3s; text-decoration:none;
                   {{ request()->routeIs('dashboard') ? 'color:var(--t); background:var(--tp); box-shadow: inset 0 0 0 1px var(--tl);' : 'color:var(--gm);' }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Dashboard
                </a>
                
                <a href="{{ route('dashboard.bookings') }}" 
                   style="display:flex; align-items:center; gap:14px; padding:14px 20px; border-radius:18px; font-size:14px; font-weight:700; transition:all .3s; text-decoration:none;
                   {{ request()->routeIs('dashboard.bookings') ? 'color:var(--t); background:var(--tp); box-shadow: inset 0 0 0 1px var(--tl);' : 'color:var(--gm);' }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    Mis Reservas
                </a>
                
                <a href="{{ route('profile.edit') }}" 
                   style="display:flex; align-items:center; gap:14px; padding:14px 20px; border-radius:18px; font-size:14px; font-weight:700; transition:all .3s; text-decoration:none;
                   {{ request()->routeIs('profile.edit') ? 'color:var(--t); background:var(--tp); box-shadow: inset 0 0 0 1px var(--tl);' : 'color:var(--gm);' }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Mi Perfil
                </a>

                <div style="margin-top:20px; padding-top:20px; border-top:1px solid rgba(0,0,0,0.05);">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="width:100%; display:flex; align-items:center; gap:14px; padding:14px 20px; border-radius:18px; font-size:14px; font-weight:700; color:#EF4444; background:transparent; border:none; cursor:pointer; transition:all .2s;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="reveal d1">
            @yield('dashboard_content')
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── SEARCH MIX MODULE ──────────────────
    const SearchMix = {
        rooms: (function() {
            const params = new URLSearchParams(window.location.search);
            const config = params.get('rooms_config');
            if (config) {
                try { return JSON.parse(decodeURIComponent(config)); } catch(e) { console.error('Rooms config error:', e); }
            }
            return [{ adults: parseInt(params.get('adults')) || 2, children: [] }];
        })(),
        
        toggleGuest() {
            const panel = document.getElementById('guest-hub-panel');
            if(panel) {
                panel.classList.toggle('active');
                if (panel.classList.contains('active')) this.renderRooms();
            }
        },

        adjustRoom(idx, type, delta) {
            const r = this.rooms[idx-1];
            if (!r) return;
            if (type === 'adults') { r.adults = Math.max(1, Math.min(6, r.adults + delta)); }
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
            const children = this.rooms.reduce((s, r) => s + r.children.length, 0);
            const rooms = this.rooms.length;
            const total = adults + children;
            const adultsInput = document.getElementById('adults-input');
            const childrenInput = document.getElementById('children-input');
            const roomsInput = document.getElementById('rooms-input');
            const roomsConfigInput = document.getElementById('rooms-config-input');
            const guestSummary = document.getElementById('guest-summary');

            if(adultsInput) adultsInput.value = adults;
            if(childrenInput) childrenInput.value = children;
            if(roomsInput) roomsInput.value = rooms;
            if(roomsConfigInput) roomsConfigInput.value = JSON.stringify(this.rooms);
            
            if(guestSummary) {
                let label = children > 0 ? (total > 1 ? 'huéspedes' : 'huésped') : (adults > 1 ? 'adultos' : 'adulto');
                guestSummary.textContent = `${total} ${label} · ${rooms} hab`;
            }
        },

        _destTimer: null,
        onDestInput(val) {
            const box = document.getElementById('dest-suggestions');
            clearTimeout(this._destTimer);
            if (val.length < 3) { if(box) box.style.display = 'none'; return; }
            this._destTimer = setTimeout(() => this.fetchSuggestions(val), 300);
        },

        async fetchSuggestions(query) {
            const box = document.getElementById('dest-suggestions');
            try {
                const res = await fetch('/api/suggest', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ q: query, language: 'es' })
                });
                const data = await res.json();
                const items = data?.data || [];
                if (!items.length) { if(box) box.style.display = 'none'; return; }
                if(box) {
                    box.innerHTML = items.slice(0, 5).map(item => `
                        <div style="padding:10px 14px; cursor:pointer; font-size:13px; border-bottom:1px solid #f0ede8;" 
                             onclick="SearchMix.selectDest(${item.id}, '${item.name.replace(/'/g, "\\'")}')">
                            ${item.type==='hotel'?'🏨':'📍'} <strong>${item.name}</strong> <small style="color:#999">${item.country_name || ''}</small>
                        </div>
                    `).join('');
                    box.style.display = 'block';
                }
            } catch(e) { console.error(e); }
        },

        selectDest(id, name) {
            const dest = document.getElementById('dest');
            const reg = document.getElementById('region-id-input');
            const box = document.getElementById('dest-suggestions');
            if(dest) dest.value = name;
            if(reg) reg.value = id;
            if(box) box.style.display = 'none';
        },

        onCheckinChange(val) {
            const cout = document.getElementById('cout');
            if (cout) cout.min = val;
        },

        validateSearch(e) {
            const regId = document.getElementById('region-id-input');
            if (!regId || !regId.value) {
                alert('Por favor selecciona un destino de la lista');
                e.preventDefault();
                return false;
            }
            return true;
        }
    };
</script>
@endpush
