<x-layouts::auth :title="__('Register')">
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
            <button type="button" class="tab-btn" data-tab="login" onclick="window.location.href='{{ $teamInvitation ? route('login', ['invitation' => $teamInvitation['code']]) : route('login') }}'">Iniciar sesión</button>
            <button type="button" class="tab-btn active" data-tab="registro">Registrarse</button>
        </div>

        <!-- FORMULARIO REGISTRO -->
        <div class="tab-content active" id="registro">
            <h1>CREAR CUENTA</h1>
            <p class="subtitle">Únete al lobby y empieza a jugar</p>

            {{-- Estado de sesión --}}
            <x-auth-session-status class="text-center" :status="session('status')" />

            @if ($teamInvitation)
                <x-team-invitation-alert :invitation="$teamInvitation" :action="__('Register')" />
            @endif

            <form method="POST" action="{{ route('register.store') }}">
                @csrf

                <div class="input-group">
                    <label for="name">Nickname</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Ej: ShadowKnight99"
                        autofocus
                        autocomplete="name"
                        required
                    >
                    @error('name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-group">
                    <label for="email">Correo electrónico</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="tucorreo@ejemplo.com"
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
                        autocomplete="new-password"
                        passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                        required
                    >
                    @error('password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-group">
                    <label for="password_confirmation">Confirmar contraseña</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="••••••••"
                        autocomplete="new-password"
                        passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                        required
                    >
                    @error('password_confirmation')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-login" data-test="register-user-button">Crear cuenta</button>
            </form>

            <div class="switch-note">
                <span>¿Ya tienes cuenta?</span>
                <a
                    href="{{ $teamInvitation ? route('login', ['invitation' => $teamInvitation['code']]) : route('login') }}"
                    data-test="team-invitation-login-link"
                    wire:navigate
                >Inicia sesión</a>
            </div>
        </div>

    </div>

</x-layouts::auth>