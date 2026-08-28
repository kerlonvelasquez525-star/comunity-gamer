<x-layouts::auth :title="__('Log in')">
    <link rel="stylesheet" href="{{ asset('css/style_sb.css') }}">

    <a href="{{ route('home') }}" class="logo-corner-link">
        <img src="{{ asset('nexus.png') }}" alt="Logo Comunidad Gamer" class="logo-corner">
    </a>

    <div class="login-container">
        <div class="login-header">
            <a href="{{ route('home') }}" class="back-link">&larr; Volver al inicio</a>
        </div>
        <span class="logo-tag">// Comunidad Gamer</span>

        <div class="tabs">
            <button type="button" class="tab-btn active" data-tab="login">Iniciar sesión</button>
            <button type="button" class="tab-btn" data-tab="registro" onclick="window.location.href='{{ $teamInvitation ? route('register', ['invitation' => $teamInvitation['code']]) : route('register') }}'">Registrarse</button>
        </div>

        <!-- FORMULARIO LOGIN -->
        <div class="tab-content active" id="login">
            <h1>PLAYER LOGIN</h1>
            <p class="subtitle">Ingresa tus credenciales para entrar al lobby</p>

            {{-- Estado de sesión (ej. enlace de restablecimiento enviado) --}}
            <x-auth-session-status class="text-center" :status="session('status')" />

            @if ($teamInvitation)
                <x-team-invitation-alert :invitation="$teamInvitation" :action="__('Log in')" />
            @endif

            <x-passkey-verify />

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="input-group">
                    <label for="email">Nickname o Correo</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Ej: correo@ejemplo.com"
                        autofocus
                        autocomplete="email"
                        required
                    >
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="options">
                    <label><input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Recordarme</label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" wire:navigate>¿Olvidaste tu contraseña?</a>
                    @endif
                </div>

                <button type="submit" class="btn-login" data-test="login-button">Entrar al juego</button>
            </form>

            <div class="switch-note">
                <span>¿No tienes cuenta?</span>
                <a
                    href="{{ $teamInvitation ? route('register', ['invitation' => $teamInvitation['code']]) : route('register') }}"
                    data-test="register-link"
                    wire:navigate
                >Regístrate</a>
            </div>
        </div>

    </div>

</x-layouts::auth>