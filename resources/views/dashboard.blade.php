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
                ['label' => 'Noticias',       'value' => $noticias_totales,     'route' => 'noticias.index', 'theme' => 'news', 'icon' => 'newspaper', 'caption' => 'Señales de la comunidad'],
                ['label' => 'Comunidades',    'value' => $comunidades_totales, 'route' => 'public-comunidad.index', 'theme' => 'communities', 'icon' => 'user-group', 'caption' => 'Escuadras conectadas'],
                ['label' => 'Casos abiertos', 'value' => $problemas_abiertos,   'route' => 'problemas.index', 'theme' => 'issues', 'icon' => 'exclamation-triangle', 'caption' => 'Requieren atención'],
            ] as $card)
                <a href="{{ route($card['route'], $card['route'] === 'public-comunidad.index' ? [] : [$team->slug]) }}" wire:navigate
                   class="metric-card metric-card--{{ $card['theme'] }} rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="metric-card-topline">
                        <span class="metric-card-icon"><flux:icon :name="$card['icon']" class="size-5" /></span>
                        <span class="metric-card-signal">EN LÍNEA</span>
                    </div>
                    <p class="metric-card-label">{{ $card['label'] }}</p>
                    <p class="metric-card-value">{{ $card['value'] }}</p>
                    <p class="metric-card-caption">{{ $card['caption'] }}</p>
                    <span class="metric-card-bar" aria-hidden="true"></span>
                </a>
            @endforeach
        </div>

        <nav class="dashboard-quick-links" aria-label="Accesos rápidos de la comunidad">
            <a href="{{ route('noticias.index', $team->slug) }}" wire:navigate>
                <span class="dashboard-quick-icon"><flux:icon.newspaper class="size-4" /></span>
                <span><strong>Noticias</strong><small>Publica y conversa</small></span>
                <span class="dashboard-quick-arrow">&rarr;</span>
            </a>
            <a href="{{ route('public-comunidad.index') }}" wire:navigate>
                <span class="dashboard-quick-icon"><flux:icon.user-group class="size-4" /></span>
                <span><strong>Comunidades</strong><small>Gestiona tus grupos</small></span>
                <span class="dashboard-quick-arrow">&rarr;</span>
            </a>
            <a href="{{ route('problemas.index', $team->slug) }}" wire:navigate>
                <span class="dashboard-quick-icon"><flux:icon.exclamation-triangle class="size-4" /></span>
                <span><strong>Soporte</strong><small>Revisa casos abiertos</small></span>
                <span class="dashboard-quick-arrow">&rarr;</span>
            </a>
            <a href="{{ route('chatbot.index') }}" wire:navigate>
                <span class="dashboard-quick-icon"><flux:icon.sparkles class="size-4" /></span>
                <span><strong>Asistente</strong><small>Obtén ayuda rápida</small></span>
                <span class="dashboard-quick-arrow">&rarr;</span>
            </a>
        </nav>

        <section class="dashboard-live-strip" aria-labelledby="live-activity-title">
            <div class="dashboard-live-heading">
                <span class="dashboard-live-pulse" aria-hidden="true"></span>
                <div>
                    <p class="dashboard-section-kicker">ACTIVIDAD EN VIVO</p> 
                    <h2 id="live-activity-title">Lo último en Nexus</h2>
                </div>
                <span class="dashboard-live-status">Sistema activo</span>
            </div>
            <div class="dashboard-activity-list">
                @forelse ($notificaciones_recientes as $notification)
                    <a href="{{ data_get($notification->data, 'url', route('notificaciones.index')) }}" class="dashboard-activity-item">
                        <span class="dashboard-activity-icon">{{ $notification->leida_en ? '•' : '!' }}</span>
                        <span>
                            <strong>{{ data_get($notification->data, 'title', ucfirst($notification->tipo)) }}</strong>
                            <small>{{ data_get($notification->data, 'message', 'Hay una actualización en tu comunidad.') }}</small>
                        </span>
                        <time>{{ $notification->created_at?->diffForHumans() }}</time>
                    </a>
                @empty
                    <p class="dashboard-activity-empty">Tu actividad aparecerá aquí cuando haya novedades.</p>
                @endforelse
            </div>
        </section>

        <!-- NOTICIAS RECIENTES -->
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