<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Nestay') }} — Encuentra tu nido en el mundo</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="Nestay es la plataforma premium para encontrar tu alojamiento ideal. Más de 2.4 millones de hoteles, apartamentos y villas en 190 países.">
    
    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- MASTER MIX HEADER -->
    <header id="main-header">
        <a href="{{ route('home') }}" class="logo">
            <svg width="34" height="34" viewBox="0 0 38 38" fill="none">
                <path d="M19 3.5C14 3.5 6.5 9.5 6.5 19.5L6.5 32C6.5 33.4 7.6 34.5 9 34.5L29 34.5C30.4 34.5 31.5 33.4 31.5 32L31.5 19.5C31.5 9.5 24 3.5 19 3.5Z" fill="var(--t)"/>
                <path d="M12 20C12 20 14.5 14 19 14C23.5 14 26 20 26 20" stroke="rgba(255,255,255,0.3)" stroke-width="1.6" fill="none" stroke-linecap="round"/>
                <circle cx="19" cy="24" r="5.8" fill="white"/>
            </svg>
            <span>Nestay</span>
        </a>


        @hasSection('header_search')
            <div class="nav-search-wrapper" style="justify-self: center; width: 100%; max-width: 800px;">
                @yield('header_search')
            </div>
        @else
            <ul class="nav-links">
                <li><a href="{{ route('home') }}">Inicio</a></li>
                <li><a href="#how">Cómo funciona</a></li>
                <li><a href="#destinations">Explorar</a></li>
            </ul>
        @endif

        <div class="nav-right" style="display:flex; gap:12px">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-ghost">Mi Perfil</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn-primary">Salir</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-ghost">Entrar</a>
                <a href="{{ route('register') }}" class="btn-primary">Unirse</a>
            @endauth
        </div>
    </header>

    <!-- MAIN PAGE CONTENT -->
    <main>
        @yield('content')
    </main>

    <!-- MASTER MIX FOOTER -->

    <!-- MOBILE NAVIGATION (App Style) -->
    <nav class="mobile-nav">
        <a href="{{ route('home') }}" class="mn-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <svg class="mn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>Inicio</span>
        </a>
        <a href="{{ route('search') }}" class="mn-item {{ request()->routeIs('search') ? 'active' : '' }}">
            <svg class="mn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <span>Buscar</span>
        </a>
        <a href="{{ route('dashboard.bookings') }}" class="mn-item {{ request()->routeIs('dashboard.bookings') ? 'active' : '' }}">
            <svg class="mn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            <span>Mis Viajes</span>
        </a>
        <a href="{{ route('dashboard') }}" class="mn-item {{ request()->routeIs('dashboard') || request()->routeIs('profile.edit') ? 'active' : '' }}">
            <svg class="mn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Perfil</span>
        </a>
    </nav>
    <footer style="background:var(--v); color:#fff; padding:80px 64px 32px; margin-top:0">
        <div style="display:grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap:64px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom:64px">
            <div>
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:24px">
                    <svg width="30" height="30" viewBox="0 0 38 38" fill="none"><path d="M19 3.5C14 3.5 6.5 9.5 6.5 19.5L6.5 32C6.5 33.4 7.6 34.5 9 34.5L29 34.5C30.4 34.5 31.5 33.4 31.5 32L31.5 19.5C31.5 9.5 24 3.5 19 3.5Z" fill="var(--t)"/></svg>
                    <span style="font-family:'Fraunces',serif; font-size:24px; font-weight:800">Nestay</span>
                </div>
                <p style="font-size:15px; opacity:0.6; line-height:1.7; max-width:300px">"No buscamos hoteles. Encontramos tu próximo nido en el mundo."</p>
                <div style="margin-top:24px; display:flex; gap:16px">
                    <span style="opacity:0.4; font-size:13px">✦ 2.4M Alojamientos</span>
                    <span style="opacity:0.4; font-size:13px">✦ 190 Países</span>
                </div>
            </div>
            <div>
                <h5 style="font-size:11px; text-transform:uppercase; letter-spacing:1.5px; opacity:0.3; margin-bottom:24px">Servicios</h5>
                <ul style="font-size:14px; gap:12px; display:grid; font-weight:500; opacity:0.7">
                    <li><a>Hoteles</a></li>
                    <li><a>Apartamentos</a></li>
                    <li><a>Villas de Lujo</a></li>
                </ul>
            </div>
            <div>
                <h5 style="font-size:11px; text-transform:uppercase; letter-spacing:1.5px; opacity:0.3; margin-bottom:24px">Empresa</h5>
                <ul style="font-size:14px; gap:12px; display:grid; font-weight:500; opacity:0.7">
                    <li><a>Sobre Nestay</a></li>
                    <li><a>Contacto</a></li>
                    <li><a>Prensa</a></li>
                </ul>
            </div>
            <div>
                <h5 style="font-size:11px; text-transform:uppercase; letter-spacing:1.5px; opacity:0.3; margin-bottom:24px">Legal</h5>
                <ul style="font-size:14px; gap:12px; display:grid; font-weight:500; opacity:0.7">
                    <li><a>Privacidad</a></li>
                    <li><a>Términos</a></li>
                    <li><a>Cookies</a></li>
                </ul>
            </div>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; padding-top:32px; font-size:13px; opacity:0.3">
            <div style="font-size: 15px; font-weight: bold;">Powered by Viajes GPS | 7 años diseñando experiencias a medida.</div>
            <div style="display:flex; gap:24px">
                <a>Instagram</a>
                <a>LinkedIn</a>
            </div>
        </div>
    </footer>

    <!-- LOADER OVERLAY -->
    <div id="loader-overlay" class="loader-overlay">
        <div class="l-spinner"></div>
    </div>

    <script>
        // Sticky Header Scroll Logic
        window.addEventListener('scroll', () => {
            const h = document.getElementById('main-header');
            if (window.scrollY > 20) h.classList.add('sticky');
            else h.classList.remove('sticky');
        });

        function showLoader() { document.getElementById('loader-overlay').classList.add('show'); }
        function hideLoader() { document.getElementById('loader-overlay').classList.remove('show'); }
    </script>

    <!-- CUSTOM CURSOR REMOVED -->
</body>

</html>
