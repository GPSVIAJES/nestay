<section>
    <header style="margin-bottom: 24px;">
        <h2 style="font-size:22px; font-weight:800; color:var(--v); margin-bottom:4px; letter-spacing:-0.5px;">
            {{ __('Cambiar Contraseña') }}
        </h2>
        <p style="font-size:14px; color:var(--gm); opacity:0.7;">
            {{ __('Asegúrate de que tu cuenta use una contraseña larga y aleatoria para mantenerse segura.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" style="display:flex; flex-direction:column; gap:20px;">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" style="display:block; font-size:12px; font-weight:800; color:var(--gm); text-transform:uppercase; letter-spacing:1px; margin-bottom:8px; opacity:0.6;">Contraseña actual</label>
            <input id="update_password_current_password" name="current_password" type="password" 
                   style="width:100%; padding:14px 20px; border-radius:16px; border:1.5px solid rgba(0,0,0,0.08); background:var(--cr); font-family:'DM Sans',sans-serif; font-size:15px; font-weight:500; color:var(--v); outline:none; transition:all .2s;"
                   onfocus="this.style.borderColor='var(--t)'; this.style.background='#fff'; this.style.boxShadow='0 0 0 4px rgba(238, 108, 77, 0.08)';"
                   onblur="this.style.borderColor='rgba(0,0,0,0.08)'; this.style.background='var(--cr)'; this.style.boxShadow='none';"
                   autocomplete="current-password" />
            @if($errors->updatePassword->has('current_password'))
                <p style="color:#EF4444; font-size:12px; margin-top:6px; font-weight:600;">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password" style="display:block; font-size:12px; font-weight:800; color:var(--gm); text-transform:uppercase; letter-spacing:1px; margin-bottom:8px; opacity:0.6;">Nueva contraseña</label>
            <input id="update_password_password" name="password" type="password" 
                   style="width:100%; padding:14px 20px; border-radius:16px; border:1.5px solid rgba(0,0,0,0.08); background:var(--cr); font-family:'DM Sans',sans-serif; font-size:15px; font-weight:500; color:var(--v); outline:none; transition:all .2s;"
                   onfocus="this.style.borderColor='var(--t)'; this.style.background='#fff'; this.style.boxShadow='0 0 0 4px rgba(238, 108, 77, 0.08)';"
                   onblur="this.style.borderColor='rgba(0,0,0,0.08)'; this.style.background='var(--cr)'; this.style.boxShadow='none';"
                   autocomplete="new-password" />
            @if($errors->updatePassword->has('password'))
                <p style="color:#EF4444; font-size:12px; margin-top:6px; font-weight:600;">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation" style="display:block; font-size:12px; font-weight:800; color:var(--gm); text-transform:uppercase; letter-spacing:1px; margin-bottom:8px; opacity:0.6;">Confirmar nueva contraseña</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                   style="width:100%; padding:14px 20px; border-radius:16px; border:1.5px solid rgba(0,0,0,0.08); background:var(--cr); font-family:'DM Sans',sans-serif; font-size:15px; font-weight:500; color:var(--v); outline:none; transition:all .2s;"
                   onfocus="this.style.borderColor='var(--t)'; this.style.background='#fff'; this.style.boxShadow='0 0 0 4px rgba(238, 108, 77, 0.08)';"
                   onblur="this.style.borderColor='rgba(0,0,0,0.08)'; this.style.background='var(--cr)'; this.style.boxShadow='none';"
                   autocomplete="new-password" />
            @if($errors->updatePassword->has('password_confirmation'))
                <p style="color:#EF4444; font-size:12px; margin-top:6px; font-weight:600;">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div style="display:flex; align-items:center; gap:16px; margin-top:8px;">
            <button type="submit" class="btn-primary" style="padding:12px 32px;">Actualizar contraseña</button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   style="font-size:14px; color:var(--g); font-weight:700; display:flex; align-items:center; gap:6px;">
                   <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                   Contraseña actualizada
                </p>
            @endif
        </div>
    </form>
</section>
