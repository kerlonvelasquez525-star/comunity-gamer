<x-layouts::auth :title="__('Register')">
    <link rel="stylesheet" href="{{ asset('css/style_sb.css') }}">

    <a
        href="{{ route('home') }}"
        class="logo-corner-link"
        aria-label="Volver a la página anterior"
        title="Volver"
        onclick="if (window.history.length > 1) { window.history.back(); return false; }"
    >
        <img src="{{ asset('nexus.png') }}" alt="Logo Comunidad Gamer" class="logo-corner">
    </a>

    <div class="login-container">
        <div class="login-header">
            <a href="{{ route('home') }}" class="back-link">&larr; Volver al inicio</a>
        </div>
        <span class="logo-tag">// Comunidad Gamer</span>

        <div class="tabs">
            <button
                type="button"
                class="tab-btn"
                data-tab="login"
                onclick="window.location.href = '{{ $teamInvitation ? route('login', ['invitation' => $teamInvitation['code']]) : route('login') }}';"
            >
                Iniciar sesión
            </button>
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
                        required>
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
                        required>
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
                        required>
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
                        required>
                    @error('password_confirmation')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                @if ($comunidadesDisponibles->isNotEmpty())
                    <div class="community-discovery mt-4">
                        <button
                            type="button"
                            class="community-discovery-trigger"
                            aria-expanded="false"
                            onclick="const panel=document.getElementById('community-selector'); const isHidden = panel.hidden; panel.hidden = !isHidden; this.setAttribute('aria-expanded', String(!isHidden)); this.classList.toggle('is-open', !isHidden);"
                        >
                            Explorar comunidades (opcional)
                        </button>
                    </div>

                    <div id="community-selector" hidden class="community-discovery-panel mt-4">
                        <fieldset class="community-discovery-fieldset">
                            <legend>Comunidades a las que quieres solicitar ingreso (opcional)</legend>
                            <p>Selecciona una o varias. Tu ingreso quedará pendiente hasta que un administrador apruebe la solicitud.</p>
                            @foreach ($comunidadesDisponibles as $comunidad)
                                <label for="comunidad-{{ $comunidad->id }}" class="community-option">
                                    <input
                                        type="checkbox"
                                        id="comunidad-{{ $comunidad->id }}"
                                        name="comunidades[]"
                                        value="{{ $comunidad->id }}"
                                        @checked(in_array((string) $comunidad->id, (array) old('comunidades', []), true))
                                    >
                                    <span class="community-option-text">
                                        {{ $comunidad->nombre }}
                                        @if ($comunidad->juego_principal)
                                            <span class="community-option-separator">·</span>
                                            {{ $comunidad->juego_principal }}
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                            @error('comunidades')
                            <span class="field-error">{{ $message }}</span>
                            @enderror
                            @error('comunidades.*')
                            <span class="field-error">{{ $message }}</span>
                            @enderror
                        </fieldset>
                    </div>
                @else
                    <div class="mt-4 rounded-xl border border-dashed border-zinc-700 bg-zinc-950/40 p-3 text-sm text-zinc-400">
                        Por ahora no hay comunidades abiertas para solicitar ingreso.
                    </div>
                @endif

                <button type="submit" class="btn-login" data-test="register-user-button">Crear cuenta</button>
            </form>

            <div class="switch-note">
                <span>¿Ya tienes cuenta?</span>
                <a
                    href="{{ $teamInvitation ? route('login', ['invitation' => $teamInvitation['code']]) : route('login') }}"
                    data-test="team-invitation-login-link"
                    wire:navigate>Inicia sesión</a>
            </div>
        </div>

    </div>

</x-layouts::auth>