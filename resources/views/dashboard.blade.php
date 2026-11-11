<x-layouts::app :title="__('Dashboard')">
    @vite(['resources/css/dashboard.css', 'resources/css/chat.css'])

    <div class="dashboard-shell w-full space-y-8 p-6 lg:p-10">
        <header class="dashboard-welcome">
            <div class="dashboard-welcome-content">
                <p class="dashboard-section-kicker">{{ $team->name }} <span aria-hidden="true">/</span> TU ESPACIO DE JUEGO</p>
                <h1>
                    {{ now()->hour < 12 ? '¡Buenos días' : (now()->hour < 19 ? '¡Buenas tardes' : '¡Buenas noches') }},
                    {{ auth()->user()->name }}!
                </h1>
                <p>Todo lo que pasa en tu comunidad, reunido en un solo lugar.</p>
                <div class="dashboard-welcome-actions">
                    <a href="{{ route('noticias.create', $team->slug) }}" wire:navigate class="dashboard-primary-action">
                        <flux:icon.plus class="size-4" />
                        Compartir noticia
                    </a>
                    <a href="{{ route('comunidad.index', $team->slug) }}" wire:navigate class="dashboard-secondary-action">
                        Explorar comunidades <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
            <div class="dashboard-welcome-orbit" aria-hidden="true">
                <span class="dashboard-orbit-ring"></span>
                <span class="dashboard-orbit-core"><flux:icon.bolt class="size-9" /></span>
            </div>
            <time class="dashboard-welcome-date" datetime="{{ now()->toDateString() }}">{{ now()->translatedFormat('l, j \d\e F') }}</time>
        </header>

        <section aria-label="Resumen de la comunidad" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Noticias', 'value' => $noticias_totales, 'route' => 'noticias.index', 'params' => [$team->slug], 'theme' => 'news', 'icon' => 'newspaper', 'caption' => 'Novedades para descubrir'],
                ['label' => 'Comunidades', 'value' => $comunidades_totales, 'route' => 'comunidad.index', 'params' => [$team->slug], 'theme' => 'communities', 'icon' => 'user-group', 'caption' => 'Grupos conectados'],
                ['label' => 'Casos abiertos', 'value' => $problemas_abiertos, 'route' => 'problemas.index', 'params' => [$team->slug], 'theme' => 'issues', 'icon' => 'exclamation-triangle', 'caption' => 'Solicitudes por atender'],
                ['label' => 'Conversaciones', 'value' => $comentarios_totales, 'route' => 'comentarios.index', 'params' => [$team->slug], 'theme' => 'comments', 'icon' => 'chat-bubble-left-right', 'caption' => 'Voces de la comunidad'],
            ] as $card)
                <a href="{{ route($card['route'], $card['params']) }}" wire:navigate
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
        </section>

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
            <a href="{{ route('notificaciones.index') }}" wire:navigate>
                <span class="dashboard-quick-icon"><flux:icon.bell class="size-4" /></span>
                <span><strong>Notificaciones</strong><small>{{ $notificaciones_sin_leer > 0 ? $notificaciones_sin_leer.' sin leer' : 'Estás al día' }}</small></span>
                <span class="dashboard-quick-arrow">&rarr;</span>
            </a>
        </nav>

        <section class="dashboard-live-strip" aria-labelledby="live-activity-title">
            <div class="dashboard-live-heading">
                <span class="dashboard-live-pulse" aria-hidden="true"></span>
                <div>
                    <p class="dashboard-section-kicker">AL DÍA EN NEXUS</p>
                    <h2 id="live-activity-title">Tu actividad reciente</h2>
                </div>
                @if ($notificaciones_sin_leer > 0)
                    <a href="{{ route('notificaciones.index') }}" wire:navigate class="dashboard-live-status">
                        {{ $notificaciones_sin_leer }} {{ $notificaciones_sin_leer === 1 ? 'novedad' : 'novedades' }} sin leer
                    </a>
                @else
                    <span class="dashboard-live-status">Todo al día</span>
                @endif
            </div>
            <div class="dashboard-activity-list">
                @forelse ($notificaciones_recientes as $notification)
                    <a href="{{ data_get($notification->data, 'url', route('notificaciones.index')) }}" class="dashboard-activity-item {{ $notification->leida_en ? '' : 'is-unread' }}">
                        <span class="dashboard-activity-icon" aria-hidden="true">{{ $notification->leida_en ? '✓' : '!' }}</span>
                        <span>
                            <strong>{{ data_get($notification->data, 'title', ucfirst($notification->tipo)) }}</strong>
                            <small>{{ data_get($notification->data, 'message', 'Hay una actualización en tu comunidad.') }}</small>
                        </span>
                        <time>{{ $notification->created_at?->diffForHumans() }}</time>
                    </a>
                @empty
                    <div class="dashboard-activity-empty">
                        <span class="dashboard-empty-icon"><flux:icon.bell class="size-5" /></span>
                        <p><strong>Tu espacio está tranquilo.</strong><br>Cuando haya novedades, aparecerán aquí.</p>
                    </div>
                @endforelse
            </div>
            @if ($notificaciones_recientes->isNotEmpty())
                <a href="{{ route('notificaciones.index') }}" wire:navigate class="dashboard-section-link">Ver todas las notificaciones <span aria-hidden="true">&rarr;</span></a>
            @endif
        </section>

        <section class="dashboard-news-section" aria-labelledby="dashboard-news-title">
            <div class="dashboard-section-heading">
                <div>
                    <p class="dashboard-section-kicker">LO ÚLTIMO DE TU EQUIPO</p>
                    <h2 id="dashboard-news-title">Noticias recientes</h2>
                </div>
                <a href="{{ route('noticias.index', $team->slug) }}" wire:navigate class="dashboard-section-link">
                    Ver todas <span aria-hidden="true">&rarr;</span>
                </a>
            </div>

            <div class="dashboard-news-grid">
                @forelse ($noticias_recientes as $noticia)
                    <a href="{{ route('noticias.show', [$team->slug, $noticia]) }}" wire:navigate
                       class="dashboard-news-card">
                        @if ($noticia->imagen_url)
                            @php
                                $newsImageUrl = filter_var($noticia->imagen_url, FILTER_VALIDATE_URL)
                                    ? $noticia->imagen_url
                                    : (str_starts_with($noticia->imagen_url, '/') ? $noticia->imagen_url : \Illuminate\Support\Facades\Storage::disk('public')->url($noticia->imagen_url));
                            @endphp
                            <img src="{{ $newsImageUrl }}" alt="" class="dashboard-news-image" loading="lazy">
                        @endif
                        <span class="dashboard-news-meta">
                            <span>{{ $noticia->es_oficial ? 'OFICIAL' : ($noticia->categoria ?: 'NOTICIA') }}</span>
                            <time>{{ $noticia->created_at?->diffForHumans() }}</time>
                        </span>
                        <h3>{{ $noticia->titulo }}</h3>
                        <p class="dashboard-news-excerpt">{{ $noticia->contenido }}</p>
                        <span class="dashboard-news-footer">
                            <span>{{ $noticia->equipo?->name ?? $team->name }} · {{ $noticia->autor?->name ?? 'Equipo editorial' }}</span>
                            <span class="dashboard-news-read">Leer <span aria-hidden="true">&rarr;</span></span>
                        </span>
                    </a>
                @empty
                    <div class="dashboard-news-empty">
                        <span class="dashboard-empty-icon"><flux:icon.newspaper class="size-5" /></span>
                        <div>
                            <h3>La primera noticia puede ser tuya</h3>
                            <p>Comparte una novedad, una estrategia o una invitación con tu equipo.</p>
                        </div>
                        <a href="{{ route('noticias.create', $team->slug) }}" wire:navigate class="dashboard-primary-action">Crear noticia</a>
                    </div>
                @endforelse
        </section>

    </div>

</x-layouts::app>