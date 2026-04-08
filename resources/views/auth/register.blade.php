<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crear cuenta — {{ config('app.name', 'Nestay') }}</title>
    <meta name="description" content="Crea tu cuenta en Nestay y comienza a descubrir tu nido perfecto entre más de 2.4 millones de alojamientos en el mundo.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── AUTH PAGE OVERRIDES ── */
        body { cursor: auto; }
        #cursor, #cursor-ring { display: none; }

        .auth-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            font-family: 'DM Sans', sans-serif;
            background: var(--cr);
        }

        /* ── LEFT: HERO VISUAL ── */
        .auth-hero {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 64px;
        }

        .auth-hero-bg {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.6) 100%),
                url('https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&q=80&w=1400');
            background-size: cover;
            background-position: center;
            transition: transform 8s ease;
        }

        .auth-hero:hover .auth-hero-bg {
            transform: scale(1.04);
        }

        .auth-hero-content {
            position: relative;
            z-index: 10;
        }

        .auth-hero-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 22px;
            color: #fff;
            text-decoration: none;
            position: absolute;
            top: 48px;
            left: 64px;
            z-index: 20;
        }

        .auth-hero h2 {
            font-size: 48px;
            font-weight: 800;
            color: #fff;
            line-height: 1.05;
            letter-spacing: -2px;
            margin-bottom: 16px;
        }

        .auth-hero h2 em {
            font-style: italic;
            font-weight: 400;
            opacity: 0.85;
        }

        .auth-hero-sub {
            font-size: 16px;
            color: rgba(255,255,255,0.75);
            line-height: 1.6;
            max-width: 380px;
        }

        .auth-hero-features {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 40px;
            padding-top: 32px;
            border-top: 1px solid rgba(255,255,255,0.15);
        }

        .auth-feature {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .auth-feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .auth-feature-icon svg {
            width: 18px;
            height: 18px;
            color: #fff;
        }

        .auth-feature-text {
            font-size: 14px;
            font-weight: 600;
            color: rgba(255,255,255,0.9);
        }

        /* ── RIGHT: FORM PANEL ── */
        .auth-form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px;
            background: var(--cr);
            position: relative;
            overflow-y: auto;
        }

        .auth-form-wrapper {
            width: 100%;
            max-width: 420px;
            animation: authFadeUp 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes authFadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .auth-form-header {
            margin-bottom: 32px;
        }

        .auth-form-header h1 {
            font-size: 32px;
            font-weight: 800;
            color: var(--v);
            margin-bottom: 8px;
        }

        .auth-form-header p {
            font-size: 15px;
            color: var(--gm);
            line-height: 1.6;
        }

        /* ── FORM FIELDS ── */
        .auth-field {
            margin-bottom: 18px;
        }

        .auth-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            color: var(--gm);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }

        .auth-label-icon {
            width: 14px;
            height: 14px;
            color: var(--t);
            flex-shrink: 0;
        }

        .auth-input {
            width: 100%;
            padding: 14px 18px;
            border-radius: 16px;
            border: 1.5px solid rgba(0,0,0,0.08);
            background: var(--wh);
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 500;
            color: var(--v);
            transition: all 0.25s ease;
            outline: none;
        }

        .auth-input::placeholder {
            color: #b5b5b0;
        }

        .auth-input:focus {
            border-color: var(--t);
            box-shadow: 0 0 0 4px rgba(238, 108, 77, 0.08);
        }

        .auth-error {
            margin-top: 6px;
            font-size: 12px;
            color: #EE4B2B;
            font-weight: 500;
        }

        /* ── PASSWORD GRID ── */
        .auth-pw-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        /* ── SUBMIT BUTTON ── */
        .auth-submit {
            width: 100%;
            padding: 16px;
            background: var(--t);
            color: #fff;
            border: none;
            border-radius: 16px;
            font-family: 'DM Sans', sans-serif;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 8px;
        }

        .auth-submit:hover {
            background: var(--td);
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(238, 108, 77, 0.25);
        }

        .auth-submit:active {
            transform: translateY(0);
        }

        /* ── TERMS ── */
        .auth-terms {
            font-size: 12px;
            color: #b5b5b0;
            text-align: center;
            margin-top: 16px;
            line-height: 1.5;
        }

        .auth-terms a {
            color: var(--t);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-terms a:hover {
            color: var(--td);
        }

        /* ── DIVIDER ── */
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 24px 0;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(0,0,0,0.07);
        }

        .auth-divider span {
            font-size: 12px;
            color: #b5b5b0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── LOGIN SWITCH ── */
        .auth-switch {
            text-align: center;
            font-size: 14px;
            color: var(--gm);
        }

        .auth-switch a {
            font-weight: 700;
            color: var(--t);
            text-decoration: none;
            transition: color 0.2s;
        }

        .auth-switch a:hover {
            color: var(--td);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            .auth-shell {
                grid-template-columns: 1fr;
            }

            .auth-hero {
                min-height: 260px;
                padding: 40px 32px;
            }

            .auth-hero-logo {
                top: 24px;
                left: 32px;
            }

            .auth-hero h2 {
                font-size: 30px;
                letter-spacing: -1px;
            }

            .auth-hero-sub {
                font-size: 14px;
            }

            .auth-hero-features {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 12px;
                margin-top: 24px;
                padding-top: 20px;
            }

            .auth-feature-text {
                font-size: 12px;
            }

            .auth-form-panel {
                padding: 32px 24px 48px;
            }
        }

        @media (max-width: 640px) {
            .auth-hero {
                min-height: 200px;
                padding: 32px 24px;
            }

            .auth-hero-logo {
                top: 20px;
                left: 24px;
                font-size: 18px;
            }

            .auth-hero h2 {
                font-size: 24px;
            }

            .auth-hero-features {
                display: none;
            }

            .auth-form-header h1 {
                font-size: 24px;
            }

            .auth-pw-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <!-- LEFT: HERO VISUAL -->
        <div class="auth-hero">
            <a href="{{ route('home') }}" class="auth-hero-logo">
                <svg width="30" height="30" viewBox="0 0 38 38" fill="none">
                    <path d="M19 3.5C14 3.5 6.5 9.5 6.5 19.5L6.5 32C6.5 33.4 7.6 34.5 9 34.5L29 34.5C30.4 34.5 31.5 33.4 31.5 32L31.5 19.5C31.5 9.5 24 3.5 19 3.5Z" fill="var(--t)"/>
                    <path d="M12 20C12 20 14.5 14 19 14C23.5 14 26 20 26 20" stroke="rgba(255,255,255,0.3)" stroke-width="1.6" fill="none" stroke-linecap="round"/>
                    <circle cx="19" cy="24" r="5.8" fill="white"/>
                </svg>
                <span>Nestay</span>
            </a>
            <div class="auth-hero-bg"></div>
            <div class="auth-hero-content">
                <h2>Encuentra tu<br><em>nido perfecto.</em></h2>
                <p class="auth-hero-sub">Únete a miles de viajeros que ya disfrutan de la experiencia Nestay. Registro gratuito, beneficios inmediatos.</p>

                <div class="auth-hero-features">
                    <div class="auth-feature">
                        <div class="auth-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <span class="auth-feature-text">Pago 100% seguro</span>
                    </div>
                    <div class="auth-feature">
                        <div class="auth-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <span class="auth-feature-text">Confirmación inmediata</span>
                    </div>
                    <div class="auth-feature">
                        <div class="auth-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                        </div>
                        <span class="auth-feature-text">Mejor precio garantizado</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: FORM PANEL -->
        <div class="auth-form-panel">
            <div class="auth-form-wrapper">
                <div class="auth-form-header">
                    <h1>Crea tu cuenta</h1>
                    <p>Regístrate gratis y empieza a descubrir nidos únicos en todo el mundo.</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="auth-field">
                        <label for="name" class="auth-label">
                            <svg class="auth-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Nombre completo
                        </label>
                        <input id="name" class="auth-input" type="text" name="name"
                               value="{{ old('name') }}" required autofocus autocomplete="name"
                               placeholder="Tu nombre">
                        @error('name')
                            <p class="auth-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="auth-field">
                        <label for="email" class="auth-label">
                            <svg class="auth-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            Correo electrónico
                        </label>
                        <input id="email" class="auth-input" type="email" name="email"
                               value="{{ old('email') }}" required autocomplete="username"
                               placeholder="tu@correo.com">
                        @error('email')
                            <p class="auth-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Passwords side by side -->
                    <div class="auth-pw-row">
                        <div class="auth-field">
                            <label for="password" class="auth-label">
                                <svg class="auth-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                Contraseña
                            </label>
                            <input id="password" class="auth-input" type="password" name="password"
                                   required autocomplete="new-password"
                                   placeholder="Min. 8 caracteres">
                            @error('password')
                                <p class="auth-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="auth-field">
                            <label for="password_confirmation" class="auth-label">
                                <svg class="auth-label-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><circle cx="12" cy="16" r="1"/></svg>
                                Confirmar
                            </label>
                            <input id="password_confirmation" class="auth-input" type="password"
                                   name="password_confirmation" required autocomplete="new-password"
                                   placeholder="Repetir contraseña">
                            @error('password_confirmation')
                                <p class="auth-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="auth-submit">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="8.5" cy="7" r="4"/>
                            <line x1="20" y1="8" x2="20" y2="14"/>
                            <line x1="23" y1="11" x2="17" y2="11"/>
                        </svg>
                        Crear mi cuenta
                    </button>

                    <p class="auth-terms">
                        Al registrarte, aceptas nuestros <a href="#">Términos de uso</a>
                        y la <a href="#">Política de privacidad</a>.
                    </p>
                </form>

                <div class="auth-divider"><span>o</span></div>

                <div class="auth-switch">
                    ¿Ya tienes una cuenta?
                    <a href="{{ route('login') }}">Inicia sesión</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
