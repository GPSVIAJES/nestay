@extends('layouts.dashboard')

@section('dashboard_content')
    <div style="margin-bottom:40px;">
        <span style="font-size:12px; font-weight:800; text-transform:uppercase; color:var(--t); letter-spacing:1.5px; background:var(--tp); padding:6px 14px; border-radius:100px; border:1px solid var(--tl);">Configuración de cuenta</span>
        <h1 style="font-size:48px; font-weight:800; color:var(--v); letter-spacing:-2px; margin-top:16px; line-height:1;">Tu Perfil</h1>
    </div>

    <div style="display:flex; flex-direction:column; gap:32px;">
        <!-- PROFILE INFO -->
        <div style="background:var(--wh); border-radius:32px; padding:40px; border:1px solid rgba(0,0,0,0.05); box-shadow:var(--sh);">
            <div style="max-width:600px;">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- PASSWORD UPDATE -->
        <div style="background:var(--wh); border-radius:32px; padding:40px; border:1px solid rgba(0,0,0,0.05); box-shadow:var(--sh);">
            <div style="max-width:600px;">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- DELETE ACCOUNT -->
        <div style="background:#FFF5F5; border-radius:32px; padding:40px; border:1px solid rgba(254,226,226,1); box-shadow:var(--sh);">
            <div style="max-width:600px;">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection
