<section>
    <div style="margin-top: 10px;">

    <button type="button"
            style="padding:12px 24px; background:#EF4444; color:#fff; border:none; border-radius:14px; font-weight:700; font-size:14px; cursor:pointer; transition:all .2s;"
            onmouseover="this.style.background='#DC2626'; this.style.transform='translateY(-1px)';"
            onmouseout="this.style.background='#EF4444'; this.style.transform='translateY(0)';"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        {{ __('Eliminar cuenta permanentemente') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" style="padding:40px;">
            @csrf
            @method('delete')

            <h2 style="font-size:24px; font-weight:800; color:var(--v); margin-bottom:12px; letter-spacing:-1px;">
                ¿Estás seguro de que quieres eliminar tu cuenta?
            </h2>

            <p style="font-size:15px; color:var(--gm); margin-bottom:32px; line-height:1.6;">
                {{ __('Por favor, introduce tu contraseña para confirmar que deseas eliminar permanentemente tu cuenta y todos sus datos asociados.') }}
            </p>

            <div>
                <label for="password" style="display:block; font-size:12px; font-weight:800; color:var(--gm); text-transform:uppercase; letter-spacing:1px; margin-bottom:8px; opacity:0.6;">Tu contraseña</label>
                <input id="password" name="password" type="password" 
                       style="width:100%; padding:14px 20px; border-radius:16px; border:1.5px solid rgba(0,0,0,0.08); background:var(--cr); font-family:'DM Sans',sans-serif; font-size:15px; font-weight:500; color:var(--v); outline:none;"
                       placeholder="Contraseña" />
                @if($errors->userDeletion->has('password'))
                    <p style="color:#EF4444; font-size:12px; margin-top:6px; font-weight:600;">{{ $errors->userDeletion->first('password') }}</p>
                @endif
            </div>

            <div style="margin-top:40px; display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" 
                        style="padding:12px 24px; background:var(--cr); color:var(--gm); border:none; border-radius:14px; font-weight:700; font-size:14px; cursor:pointer;"
                        x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </button>

                <button type="submit" 
                        style="padding:12px 24px; background:#EF4444; color:#fff; border:none; border-radius:14px; font-weight:700; font-size:14px; cursor:pointer;">
                    {{ __('Sí, eliminar cuenta') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
