@php
    $teamSlug = $teamSlug ?? (isset($team) ? $team->slug : (auth()->user()?->currentTeam?->slug ?? request()->route('current_team')));

    $navigationItems = $teamSlug ? [
        ['label' => __('Dashboard'), 'icon' => 'home', 'route' => 'dashboard', 'pattern' => 'dashboard'],
        ['label' => __('Noticias'), 'icon' => 'newspaper', 'route' => 'noticias.index', 'pattern' => 'noticias.*'],
        ['label' => __('Comunidades'), 'icon' => 'user-group', 'route' => 'comunidad.index', 'pattern' => 'comunidad.*'],
        ['label' => __('Problemas'), 'icon' => 'exclamation-triangle', 'route' => 'problemas.index', 'pattern' => 'problemas.*'],
        ['label' => __('Asistente'), 'icon' => 'sparkles', 'route' => 'chatbot.index', 'pattern' => 'chatbot.*'],
    ] : [
        ['label' => __('Inicio'), 'icon' => 'home', 'route' => 'home', 'pattern' => null],
    ];
@endphp

@php
    $chatUsers = auth()->check() && auth()->user()->currentTeam
        ? auth()->user()->currentTeam->members()->whereKeyNot(auth()->id())->limit(12)->get()
        : collect();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        @vite(['resources/css/encabezado.css', 'resources/css/chat.css'])
    </head>
    <body class="min-h-screen bg-zinc-950 font-sans text-zinc-100 antialiased" x-data="{ mobileMenuOpen: false }">

        <div class="flex min-h-screen w-full flex-col">

            <!-- HEADER SUPERIOR CON ALTURA CONTROLADA -->
            <header class="app-header sticky top-0 z-50 w-full border-b border-zinc-800/80 bg-zinc-900/90 backdrop-blur-md">
                <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    
                    <!-- Lado Izquierdo: Logo contenido correctamente -->
                    <div class="flex shrink-0 items-center gap-4">
                        <a href="{{ route('home') }}"
                           wire:navigate
                           class="flex items-center transition-transform hover:scale-105"
                           title="Ir al inicio">
                            <img src="{{ asset('nexus.png') }}" alt="Nexus Logo" class="h-12 w-12 rounded-[45%] border-2 border-cyan-400/80 object-cover p-0.5 shadow-[0_0_12px_rgba(0,240,255,0.35)]">
                        </a>
                    </div>

                    <!-- Centro: Menú de Navegación (Desktop) -->
                    <nav class="hidden items-center gap-1 md:flex">
                        @foreach ($navigationItems as $item)
                            @php
                                $isActive = $item['pattern'] ? request()->routeIs($item['pattern']) : false;
                            @endphp
                            <a href="{{ $teamSlug ? route($item['route'], $teamSlug) : route($item['route']) }}"
                               wire:navigate
                               class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-all {{ $isActive ? 'bg-zinc-800/80 text-amber-400 font-semibold shadow-sm' : 'text-zinc-400 hover:bg-zinc-800/40 hover:text-zinc-200' }}">
                                <flux:icon :name="$item['icon']" class="size-4 shrink-0 {{ $isActive ? 'text-amber-400' : 'text-zinc-400' }}" />
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </nav>

                    <!-- Lado Derecho: Switcher de Equipo + Menú de Usuario -->
                    <div class="flex items-center gap-3">
                        @auth
                            <div class="hidden sm:block">
                                <livewire:team-switcher />
                            </div>

                            <div class="hidden sm:block border-l border-zinc-800 pl-3">
                                <x-desktop-user-menu :name="auth()->user()->name" />
                            </div>
                        @endauth

                        <!-- Hamburguesa Móvil -->
                        <button
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            type="button"
                            class="flex items-center justify-center rounded-lg p-2 text-zinc-400 hover:bg-zinc-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-amber-400/50 md:hidden"
                        >
                            <flux:icon.bars-3 x-show="!mobileMenuOpen" class="size-6 text-amber-400" />
                            <flux:icon.x-mark x-show="mobileMenuOpen" class="size-6 text-amber-400" x-cloak />
                        </button>
                    </div>
                </div>

                <!-- Menú desplegable Móvil -->
                <div x-show="mobileMenuOpen" x-collapse class="border-b border-zinc-800 bg-zinc-900/95 px-4 pt-2 pb-4 md:hidden">
                    @auth
                        <div class="mb-4 pt-2">
                            <livewire:team-switcher />
                        </div>
                    @endauth

                    <div class="space-y-1">
                        @foreach ($navigationItems as $item)
                            @php
                                $isActive = $item['pattern'] ? request()->routeIs($item['pattern']) : false;
                            @endphp
                            <a href="{{ $teamSlug ? route($item['route'], $teamSlug) : route($item['route']) }}"
                               wire:navigate
                               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-base font-medium transition-colors {{ $isActive ? 'bg-zinc-800 text-amber-400' : 'text-zinc-400 hover:bg-zinc-800/60 hover:text-white' }}">
                                <flux:icon :name="$item['icon']" class="size-5 shrink-0 {{ $isActive ? 'text-amber-400' : 'text-zinc-400' }}" />
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>

                    @auth
                        <div class="mt-4 border-t border-zinc-800/80 pt-4">
                            <x-desktop-user-menu :name="auth()->user()->name" />
                        </div>
                    @endauth
                </div>
            </header>

            <!-- CONTENIDO DE LA PÁGINA -->
            <main class="app-main flex w-full flex-1 flex-col bg-zinc-950 px-4 py-6 sm:px-6 lg:px-8">
                <div class="mx-auto w-full max-w-7xl">
                    {{ $slot }}
                </div>
            </main>

            @auth
                <button type="button" class="global-chat-open" data-chat-open aria-controls="global-chat-drawer" aria-expanded="false">
                    <span class="global-chat-status"></span>
                    <span>Ver jugadores</span>
                </button>

                <aside id="global-chat-drawer" class="global-chat-drawer" data-chat-drawer aria-hidden="true" aria-labelledby="global-chat-title">
                    <div class="global-chat-header">
                        <div>
                            <p class="global-chat-kicker">NEXUS // MENSAJES</p>
                            <h2 id="global-chat-title">Tus jugadores</h2>
                        </div>
                        <button type="button" class="global-chat-close" data-chat-close aria-label="Cerrar panel de chat">&times;</button>
                    </div>
                    <p class="global-chat-copy">Selecciona la foto o el nombre de un usuario para abrir su conversación.</p>
                    <div class="global-chat-users">
                        @forelse ($chatUsers as $chatUser)
                            <a href="{{ route('chat.show', $chatUser) }}" class="global-chat-user">
                                <span class="global-chat-avatar">{{ strtoupper(substr($chatUser->name, 0, 1)) }}</span>
                                <span class="global-chat-user-info">
                                    <strong>{{ $chatUser->name }}</strong>
                                    <small>{{ $chatUser->email }}</small>
                                </span>
                                <span class="global-chat-arrow" aria-hidden="true">&rarr;</span>
                            </a>
                        @empty
                            <p class="global-chat-empty">No hay otros usuarios disponibles en este equipo.</p>
                        @endforelse
                    </div>
                </aside>
            @endauth

        </div>

        @auth
            <livewire:create-team-modal />
        @endauth

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts

        @auth
            <script>
                (() => {
                    const drawer = document.querySelector('[data-chat-drawer]');
                    const openButton = document.querySelector('[data-chat-open]');
                    const closeButton = document.querySelector('[data-chat-close]');

                    if (!drawer || !openButton || !closeButton) return;

                    const setDrawerState = (isOpen) => {
                        drawer.classList.toggle('is-open', isOpen);
                        drawer.setAttribute('aria-hidden', String(!isOpen));
                        openButton.setAttribute('aria-expanded', String(isOpen));
                    };

                    openButton.addEventListener('click', () => setDrawerState(true));
                    closeButton.addEventListener('click', () => setDrawerState(false));
                    document.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape') setDrawerState(false);
                    });
                })();
            </script>
        @endauth
    </body>
</html>