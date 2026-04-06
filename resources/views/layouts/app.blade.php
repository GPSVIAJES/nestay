<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Nestay') }} — Tu nido en cada rincón del mundo</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="Encuentra tu próximo nido en el mundo. Más de 2.4 millones de alojamientos en 190 países con confirmación inmediata y el mejor precio garantizado.">
    
    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- CUSTOM CURSOR -->
    <div id="cursor"></div>
    <div id="cursor-ring"></div>

    <!-- MAIN NAVIGATION -->
    <nav>
        <a href="{{ route('home') }}" class="nav-logo">
            <svg width="38" height="38" viewBox="0 0 38 38" fill="none">
                <path d="M19 3.5C14 3.5 6.5 9.5 6.5 19.5L6.5 32C6.5 33.4 7.6 34.5 9 34.5L29 34.5C30.4 34.5 31.5 33.4 31.5 32L31.5 19.5C31.5 9.5 24 3.5 19 3.5Z" fill="#E07A5F"/>
                <path d="M12 20C12 20 14.5 14 19 14C23.5 14 26 20 26 20" stroke="white" stroke-width="1.6" fill="none" stroke-linecap="round"/>
                <circle cx="19" cy="24" r="5.8" fill="white"/>
                <ellipse cx="19" cy="23" rx="2.7" ry="3.3" fill="#81B29A"/>
                <path d="M15.8 28L19 33.5L22.2 28" fill="white"/>
            </svg>
            <span class="nav-brand">Nestay</span>
        </a>

        <ul class="nav-links">
            <li><a href="{{ route('home') }}">Inicio</a></li>
            <li><a href="#how">Cómo funciona</a></li>
            <li><a href="#destinations">Destinos</a></li>
            <li><a href="#featured">Ofertas</a></li>
        </ul>

        <div class="nav-right">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-ghost">Mi cuenta</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-primary">Salir</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-ghost">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="btn-primary">Registrarse</a>
            @endauth
        </div>
    </nav>

    <!-- PAGE CONTENT -->
    <main>
        @if(session('success'))
            <div id="toast" class="toast show">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div id="toast" class="toast show" style="background:#ef4444">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer>
        <div class="ft">
            <div class="fb">
                <div class="flogo">
                    <svg width="32" height="32" viewBox="0 0 38 38" fill="none"><path d="M19 3.5C14 3.5 6.5 9.5 6.5 19.5L6.5 32C6.5 33.4 7.6 34.5 9 34.5L29 34.5C30.4 34.5 31.5 33.4 31.5 32L31.5 19.5C31.5 9.5 24 3.5 19 3.5Z" fill="#E07A5F"/><path d="M12 20C12 20 14.5 14 19 14C23.5 14 26 20 26 20" stroke="white" stroke-width="1.6" fill="none" stroke-linecap="round"/><circle cx="19" cy="24" r="5.8" fill="white"/><ellipse cx="19" cy="23" rx="2.7" ry="3.3" fill="#81B29A"/><path d="M15.8 28L19 33.5L22.2 28" fill="white"/></svg>
                    <span class="fbrand">Nestay</span>
                </div>
                <p class="ftagline" style="font-family:'Instrument Serif',serif;font-style:italic">"Tu nido en cada rincón del mundo." 2.4M+ alojamientos, 190 países.</p>
            </div>
            <div class="fcol">
                <h4>Alojamientos</h4>
                <ul>
                    <li><a>Hoteles</a></li>
                    <li><a>Apartamentos</a></li>
                    <li><a>Casas rurales</a></li>
                    <li><a>Villas</a></li>
                </ul>
            </div>
            <div class="fcol">
                <h4>Destinos</h4>
                <ul>
                    <li><a>España</a></li>
                    <li><a>Portugal</a></li>
                    <li><a>Francia</a></li>
                    <li><a>Italia</a></li>
                </ul>
            </div>
            <div class="fcol">
                <h4>Nestay</h4>
                <ul>
                    <li><a>Sobre nosotros</a></li>
                    <li><a>Ayuda</a></li>
                    <li><a>Contacto</a></li>
                    <li><a>Privacidad</a></li>
                </ul>
            </div>
        </div>
        <div class="fbot">
            <div class="fcopy">© {{ date('Y') }} <span>Nestay</span> · "Tu nido en cada rincón del mundo" ✦</div>
            <div class="fbadges">
                <span class="fbadge">GDPR</span>
                <span class="fbadge">SSL Secure</span>
                <span class="fbadge">2.4M+ Alojamientos</span>
            </div>
        </div>
    </footer>

    <!-- LOADER OVERLAY -->
    <div id="loader-overlay" class="loader-overlay">
        <div class="l-spinner"></div>
        <div class="l-title">Buscando tu nido...</div>
        <div class="l-sub">Estamos revisando millones de opciones en tiempo real.</div>
    </div>

    <script>
        // Simple loader toggle
        function showLoader() { document.getElementById('loader-overlay').classList.add('show'); }
        function hideLoader() { document.getElementById('loader-overlay').classList.remove('show'); }
        
        // Form persistence for search fields
        document.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            if (params.has('destination')) document.getElementById('dest')?.value = params.get('destination');
            if (params.has('check_in')) document.getElementById('cin')?.value = params.get('check_in');
            if (params.has('check_out')) document.getElementById('cout')?.value = params.get('check_out');
        });
    </script>
</body>
</html>
