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
                ['label' => 'Comunidades',    'value' => $comunidades_totales, 'route' => 'comunidad.index', 'theme' => 'communities', 'icon' => 'user-group', 'caption' => 'Escuadras conectadas'],
                ['label' => 'Casos abiertos', 'value' => $problemas_abiertos,   'route' => 'problemas.index', 'theme' => 'issues', 'icon' => 'exclamation-triangle', 'caption' => 'Requieren atención'],
            ] as $card)
                <a href="{{ route($card['route'], $team->slug) }}" wire:navigate
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
            <a href="{{ route('comunidad.index', $team->slug) }}" wire:navigate>
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
                    <a href="{{ route('comunidad.index', $team->slug) }}" wire:navigate
                       class="text-sm font-semibold text-amber-600 hover:underline">Explorar</a>
                </div>

                <form method="GET" action="{{ route('dashboard') }}" class="grid gap-3 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900 md:grid-cols-2 xl:grid-cols-3">
                    <input type="search" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar comunidad..." aria-label="Buscar comunidad"
                           class="col-span-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">

                    @foreach (['juego_principal' => 'Juego', 'plataforma' => 'Plataforma', 'region' => 'Región', 'idioma' => 'Idioma', 'modalidad' => 'Modalidad', 'horario' => 'Horario', 'tipo' => 'Estilo', 'nivel' => 'Nivel', 'rango' => 'Rango', 'estado' => 'Estado'] as $filter => $label)
                        <label class="space-y-1 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            <span>{{ $label }}</span>
                            <select name="{{ $filter }}" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">
                                <option value="">Todos</option>
                                @foreach ($filterOptions[$filter] ?? [] as $option)
                                    <option value="{{ $option }}" @selected(request($filter) === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </label>
                    @endforeach

                    <div class="col-span-full flex items-center justify-end gap-3">
                        @if (request()->hasAny(['buscar', 'juego_principal', 'plataforma', 'region', 'idioma', 'modalidad', 'horario', 'tipo', 'nivel', 'rango', 'estado']))
                            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white">Limpiar</a>
                        @endif
                        <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-zinc-950 transition hover:bg-amber-400">Filtrar</button>
                    </div>
                </form>

                <div class="dashboard-community-grid">
                @forelse ($comunidades_activas as $comunidad)
                        <article class="dashboard-community-card">
                            <div class="dashboard-community-image-wrap">
                                @php
                                    $communityImage = ! empty($comunidad->imagen_url)
                                        ? (filter_var($comunidad->imagen_url, FILTER_VALIDATE_URL)
                                            ? $comunidad->imagen_url
                                            : \Illuminate\Support\Facades\Storage::disk('public')->url($comunidad->imagen_url))
                                        : asset('nexus.png');
                                @endphp
                                <img src="{{ $communityImage }}" alt="{{ $comunidad->nombre }}" class="dashboard-community-image" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('nexus.png') }}';">
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
                                <a href="{{ route('comunidad.index', $team->slug) }}" wire:navigate class="dashboard-community-link">Ver comunidad &rarr;</a>
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