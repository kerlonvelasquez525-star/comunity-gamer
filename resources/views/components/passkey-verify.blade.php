@props([
    'optionsRoute' => 'passkey.login-options',
    'submitRoute' => 'passkey.login',
    'label' => __('Iniciar sesión con llave de acceso'),
    'loadingLabel' => __('Autenticando...'),
    'separator' => __('O continuar con correo'),
])

@assets
@vite('resources/js/passkeys.js')
@endassets

<div
    x-data="{
        supported: false,
        loading: false,
        error: null,
        updateSupport() {
            this.supported = Boolean(window.Passkeys?.isSupported());
        },
        init() {
            this.updateSupport();
            window.addEventListener('passkeys:ready', () => this.updateSupport(), { once: true });
        },
        async verify() {
            this.loading = true;
            this.error = null;
            try {
                const response = await window.Passkeys.verify({
                    routes: {
                        options: '{{ route($optionsRoute) }}',
                        submit: '{{ route($submitRoute) }}',
                    },
                });
                Livewire.navigate(response.redirect || '/dashboard');
            } catch (e) {
                if (e.constructor?.name !== 'UserCancelledError') {
                    this.error = e.message;
                }
            } finally {
                this.loading = false;
            }
        },
    }"
>
    <template x-if="supported">
        <div>
            <button
                type="button"
                x-on:click="verify()"
                x-bind:disabled="loading"
                style="
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 10px;
                    width: 100%;
                    padding: 12px 20px;
                    border: 1px solid rgba(0, 255, 231, 0.3);
                    border-radius: 10px;
                    background: rgba(0, 255, 231, 0.05);
                    color: #00ffe7;
                    font-family: inherit;
                    font-size: 14px;
                    font-weight: 600;
                    letter-spacing: 1px;
                    text-transform: uppercase;
                    cursor: pointer;
                    transition: all 0.3s ease;
                "
                onmouseover="this.style.background='rgba(0, 255, 231, 0.15)'; this.style.borderColor='rgba(0, 255, 231, 0.6)'; this.style.boxShadow='0 0 15px rgba(0, 255, 231, 0.2)';"
                onmouseout="this.style.background='rgba(0, 255, 231, 0.05)'; this.style.borderColor='rgba(0, 255, 231, 0.3)'; this.style.boxShadow='none';"
            >
                {{-- Icono de huella con tamaño fijo --}}
                <svg style="width: 20px; height: 20px; min-width: 20px; max-width: 20px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0119.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 004.5 10.5a48.667 48.667 0 00-1.39 11.49M12 10.5c0 4.02-.77 7.865-2.173 11.392M10.5 7.963a7.465 7.465 0 00-3-1.39M15 10.5a30.371 30.371 0 01-.924 7.574M8.25 10.5A2.25 2.25 0 0110.5 8.25a2.25 2.25 0 012.25 2.25c0 3.24-.534 6.358-1.519 9.276" />
                </svg>

                <span x-show="!loading">{{ $label }}</span>
                <span x-show="loading" x-cloak>{{ $loadingLabel }}</span>
            </button>

            {{-- Error --}}
            <p x-show="error" x-text="error" x-cloak
               style="margin-top: 8px; text-align: center; font-size: 13px; color: #ff4d6a;"></p>

            {{-- Separador --}}
            <div style="position: relative; margin: 20px 0; display: flex; align-items: center;">
                <div style="flex: 1; height: 1px; background: rgba(255,255,255,0.1);"></div>
                <span style="padding: 0 12px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.3);">
                    {{ $separator }}
                </span>
                <div style="flex: 1; height: 1px; background: rgba(255,255,255,0.1);"></div>
            </div>
        </div>
    </template>
</div>