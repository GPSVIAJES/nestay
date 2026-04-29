<section>
    <header style="margin-bottom: 24px;">
        <h2 style="font-size:22px; font-weight:800; color:var(--v); margin-bottom:4px; letter-spacing:-0.5px;">
            {{ __('Información del Perfil') }}
        </h2>
        <p style="font-size:14px; color:var(--gm); opacity:0.7;">
            {{ __("Actualiza la información de tu cuenta y tu dirección de correo electrónico.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" style="display:flex; flex-direction:column; gap:20px;">
        @csrf
        @method('patch')

        <div>
            <label for="name" style="display:block; font-size:12px; font-weight:800; color:var(--gm); text-transform:uppercase; letter-spacing:1px; margin-bottom:8px; opacity:0.6;">Nombre completo</label>
            <input id="name" name="name" type="text" 
                   style="width:100%; padding:14px 20px; border-radius:16px; border:1.5px solid rgba(0,0,0,0.08); background:var(--cr); font-family:'DM Sans',sans-serif; font-size:15px; font-weight:500; color:var(--v); outline:none; transition:all .2s;"
                   onfocus="this.style.borderColor='var(--t)'; this.style.background='#fff'; this.style.boxShadow='0 0 0 4px rgba(238, 108, 77, 0.08)';"
                   onblur="this.style.borderColor='rgba(0,0,0,0.08)'; this.style.background='var(--cr)'; this.style.boxShadow='none';"
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @if($errors->has('name'))
                <p style="color:#EF4444; font-size:12px; margin-top:6px; font-weight:600;">{{ $errors->first('name') }}</p>
            @endif
        </div>

        <div>
            <label for="email" style="display:block; font-size:12px; font-weight:800; color:var(--gm); text-transform:uppercase; letter-spacing:1px; margin-bottom:8px; opacity:0.6;">Correo electrónico</label>
            <input id="email" name="email" type="email" 
                   style="width:100%; padding:14px 20px; border-radius:16px; border:1.5px solid rgba(0,0,0,0.08); background:var(--cr); font-family:'DM Sans',sans-serif; font-size:15px; font-weight:500; color:var(--v); outline:none; transition:all .2s;"
                   onfocus="this.style.borderColor='var(--t)'; this.style.background='#fff'; this.style.boxShadow='0 0 0 4px rgba(238, 108, 77, 0.08)';"
                   onblur="this.style.borderColor='rgba(0,0,0,0.08)'; this.style.background='var(--cr)'; this.style.boxShadow='none';"
                   value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @if($errors->has('email'))
                <p style="color:#EF4444; font-size:12px; margin-top:6px; font-weight:600;">{{ $errors->first('email') }}</p>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div style="margin-top:16px; background:var(--tp); padding:12px; border-radius:12px; border:1px solid var(--tl);">
                    <p style="font-size:13px; color:var(--gm);">
                        {{ __('Tu correo electrónico no está verificado.') }}
                        <button form="send-verification" style="background:none; border:none; padding:0; color:var(--t); font-weight:700; cursor:pointer; text-decoration:underline;">
                            {{ __('Haz clic aquí para reenviar el email de verificación.') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p style="margin-top:8px; font-weight:700; font-size:12px; color:var(--g);">
                            {{ __('Se ha enviado un nuevo enlace de verificación a tu dirección de correo electrónico.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div style="display:flex; align-items:center; gap:16px; margin-top:8px;">
            <button type="submit" class="btn-primary" style="padding:12px 32px;">Guardar cambios</button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   style="font-size:14px; color:var(--g); font-weight:700; display:flex; align-items:center; gap:6px;">
                   <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                   Guardado con éxito
                </p>
            @endif
        </div>
    </form>
</section>
