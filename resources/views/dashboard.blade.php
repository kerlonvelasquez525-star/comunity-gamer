<x-layouts::app :title="__('Dashboard')">
    @vite(['resources/css/dashboard.css', 'resources/css/chat.css'])

    <!-- CONTENEDOR FLUIDO (Sin mx-auto ni max-w-7xl) -->
    <div class="dashboard-shell w-full space-y-12 p-6 lg:p-10">

        <!-- BANNER PRINCIPAL -->
        <div class="cyber-hero rounded-2xl bg-zinc-950 p-8 text-white shadow-xl dark:bg-black">
            <p class="text-sm font-semibold text-amber-400">{{ $team->name }}</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight">Todo lo que pasa en tu comunidad</h1>
            <p class="mt-3 max-w-2xl text-zinc-300">
                Noticias, grupos y soporte reunidos en un solo lugar.
            </p>
        </div>

        <!-- TARJETAS DE MÉTRICAS -->
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Noticias',            'value' => $noticias_totales,     'route' => 'noticias.index', 'theme' => 'news'],
                ['label' => 'Comunidades',         'value' => $comunidades_totales, 'route' => 'public-comunidad.index', 'theme' => 'communities'],
                ['label' => 'Casos abiertos',      'value' => $problemas_abiertos,   'route' => 'problemas.index', 'theme' => 'issues'],
            ] as $card)
                <a href="{{ $card['route'] === 'public-comunidad.index' ? route('public-comunidad.index') : route($card['route'], $team->slug) }}" wire:navigate
                   class="metric-card metric-card--{{ $card['theme'] }} rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $card['label'] }}</p>
                    <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">{{ $card['value'] }}</p>
                </a>
            @endforeach
        </div>

        <nav class="dashboard-quick-links" aria-label="Accesos rápidos de la comunidad">
            <a href="{{ route('noticias.index', $team->slug) }}" wire:navigate>
                <span class="dashboard-quick-icon">N</span>
                <span><strong>Noticias</strong><small>Publica y conversa</small></span>
                <span class="dashboard-quick-arrow">&rarr;</span>
            </a>
            <a href="{{ route('public-comunidad.index') }}" wire:navigate>
                <span class="dashboard-quick-icon">C</span>
                <span><strong>Comunidades</strong><small>Gestiona tus grupos</small></span>
                <span class="dashboard-quick-arrow">&rarr;</span>
            </a>
            <a href="{{ route('problemas.index', $team->slug) }}" wire:navigate>
                <span class="dashboard-quick-icon">S</span>
                <span><strong>Soporte</strong><small>Revisa casos abiertos</small></span>
                <span class="dashboard-quick-arrow">&rarr;</span>
            </a>
            <a href="{{ route('chatbot.index') }}" wire:navigate>
                <span class="dashboard-quick-icon">A</span>
                <span><strong>Asistente</strong><small>Obtén ayuda rápida</small></span>
                <span class="dashboard-quick-arrow">&rarr;</span>
            </a>
        </nav>

        <!-- NOTICIAS Y COMUNIDADES -->
        <div class="grid gap-8 lg:grid-cols-2">
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">Noticias recientes</h2>
                    <a href="{{ route('noticias.index', $team->slug) }}" wire:navigate
                       class="text-sm font-semibold text-amber-600 hover:underline">Ver todas</a>
                </div>

                @forelse ($noticias_recientes as $noticia)
                    <a href="{{ route('noticias.show', [$team->slug, $noticia]) }}" wire:navigate
                       class="block rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900">
                        <p class="font-semibold text-zinc-900 dark:text-white">{{ $noticia->titulo }}</p>
                        <p class="mt-1 text-xs font-semibold text-amber-500">{{ $noticia->equipo?->name ?? $team->name }}</p>
                        <p class="mt-2 line-clamp-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $noticia->contenido }}</p>
                        <p class="mt-3 text-xs text-zinc-400">
                            {{ $noticia->autor?->name ?? 'Autor desconocido' }} · {{ $noticia->created_at?->diffForHumans() }}
                        </p>
                    </a>
                @empty
                    <p class="rounded-xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-500 dark:border-zinc-700">
                        Todavía no hay noticias.
                    </p>
                @endforelse
            </section>

            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">Comunidades activas</h2>
                    <a href="{{ route('public-comunidad.index') }}" wire:navigate
                       class="text-sm font-semibold text-amber-600 hover:underline">Explorar</a>
                </div>

                <div class="dashboard-community-grid">
                @forelse ($comunidades_activas as $comunidad)
                        <article class="dashboard-community-card">
                            <div class="dashboard-community-image-wrap">
                                <img src="{{ asset('nexus.png') }}" alt="{{ $comunidad->nombre }}" class="dashboard-community-image" loading="lazy">
                                <span class="dashboard-community-badge">COMUNIDAD ACTIVA</span>
                            </div>
                            <div class="dashboard-community-content">
                                <p class="dashboard-community-owner">{{ $comunidad->creador?->name ?? 'Miembro de la comunidad' }}</p>
                                <h3>{{ $comunidad->nombre }}</h3>
                                <p class="dashboard-community-description">
                                    {{ $comunidad->descripcion ?: 'Una nueva comunidad gamer.' }}
                                </p>
                                <div class="dashboard-community-tags">
                            @foreach ([$comunidad->juego_principal, $comunidad->plataforma, $comunidad->modalidad] as $tag)
                                @if (filled($tag))
                                        <span>{{ $tag }}</span>
                                @endif
                            @endforeach
                                </div>
                                <div class="dashboard-community-meta">
                                    <span>{{ $comunidad->miembros_count }} {{ $comunidad->miembros_count === 1 ? 'miembro' : 'miembros' }}</span>
                                    <span class="dashboard-community-status">Activa</span>
                                </div>
                                <a href="{{ route('public-comunidad.index') }}" wire:navigate class="dashboard-community-link">Solicitar ingreso &rarr;</a>
                            </div>
                        </article>
                @empty
                    <p class="rounded-xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-500 dark:border-zinc-700">
                        Todavía no hay comunidades.
                    </p>
                @endforelse
                </div>
            </section>
        </div>

        <!-- ============================================ -->
        <!-- SECCIÓN DE RANKING (INCLUIDA)                -->
        <!-- ============================================ -->
        <section class="pt-4">
            @include('partials.ranking-preview')
        </section>

        <!-- ============================================ -->
        <!-- SECCIÓN DE CONTACTO (INCLUIDA)               -->
        <!-- ============================================ -->
        <section class="pt-4">
            @include('partials.contacto-preview')
        </section>

    </div>

</x-layouts::app>