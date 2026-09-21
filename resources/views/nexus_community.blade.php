@php
    $teamSlug = auth()->user()?->currentTeam?->slug;

    $formatCompactNumber = function ($value): string {
        $value = (float) $value;

        if ($value >= 1000000) {
            return number_format($value / 1000000, 1, ',', '').'M';
        }

        if ($value >= 1000) {
            return number_format($value / 1000, 1, ',', '').'K';
        }

        return number_format($value, 0, ',', '.');
    };

    $navLinks = [
        ['label' => 'home', 'route' => 'dashboard', 'anchor' => '#top'],
        ['label' => 'noticias', 'route' => 'noticias.index', 'anchor' => '#noticias'],
        ['label' => 'comunidad', 'route' => 'comunidad.index', 'anchor' => '#comunidad'],
        ['label' => 'problemas', 'route' => 'problemas.index', 'anchor' => '#problemas'],
    ];
@endphp

<x-layouts::public :title="__('Nexus Community')">
    <div id="top" class="public-home">

    @vite('resources/css/style_th.css')

    <livewire:pages.teams.pending-invitations-modal />

    <header>
        <div class="brand">
            <a href="{{ route('home') }}" class="brand-logo" aria-label="Ir a la página principal de Nexus Community">
                <img src="{{ asset('nexus.png') }}" alt="Nexus Community" width="48" height="48" decoding="async">
            </a>
            <a href="{{ route('home') }}" class="brand-title" aria-label="Ir a la página principal de Nexus Community">nexus-comunity</a>
        </div>

        <nav aria-label="Navegación principal" class="public-main-nav">
            @foreach ($navLinks as $index => $link)
                @php
                    $guestHref = match ($link['label']) {
                        'home' => route('home'),
                        'noticias' => route('public-noticias.index'),
                        'comunidad' => \Illuminate\Support\Facades\Route::has('public-comunidad.index')
                            ? route('public-comunidad.index')
                            : route('home'),
                        'problemas' => \Illuminate\Support\Facades\Route::has('public-problemas.index')
                            ? route('public-problemas.index')
                            : route('home'),
                        default => route('home'),
                    };
                    $authHref = match ($link['label']) {
                        'home' => route('home'),
                        'noticias' => route('public-noticias.index'),
                        'comunidad' => \Illuminate\Support\Facades\Route::has('public-comunidad.index')
                            ? route('public-comunidad.index')
                            : ($teamSlug ? route($link['route'], $teamSlug) : route('teams.index')),
                        'problemas' => \Illuminate\Support\Facades\Route::has('public-problemas.index')
                            ? route('public-problemas.index')
                            : ($teamSlug ? route($link['route'], $teamSlug) : route('teams.index')),
                        default => $teamSlug ? route($link['route'], $teamSlug) : route('teams.index'),
                    };
                @endphp

                @auth
                    <a href="{{ $authHref }}" @class(['active' => $index === 0, 'public-nav-link' => true])>
                        {{ $link['label'] }}
                    </a>
                @else
                    <a href="{{ $guestHref }}" @class(['active' => $index === 0, 'public-nav-link' => true])>
                        {{ $link['label'] }}
                    </a>
                @endauth
            @endforeach
        </nav>

        <div class="header-right">
            <div class="sys-status">
                <span class="status-dot"></span>
            </div>
            <a href="{{ $teamSlug ? route('dashboard', $teamSlug) : (auth()->check() ? route('teams.index') : route('login')) }}"
               class="btn-signin btn-signin-soft"
               aria-label="{{ $teamSlug ? 'Ir al panel' : (auth()->check() ? 'Elegir equipo' : 'Iniciar sesión') }}">
                <flux:icon name="user" class="w-4 h-4" />
            </a>
        </div>
    </header>

    <div class="hero-container">
        <section class="hero-left">
            <span class="badge-tag">Una comunidad para jugar mejor acompañado</span>

            <h1 class="hero-title">Encuentra tu próxima partida</h1>

            <p class="hero-description">
                Descubre noticias, grupos y conversaciones de jugadores que comparten tus mismos juegos.
                Nexus Community es tu lugar para encontrar gente y volver a disfrutar de jugar en compañía.
            </p>

            <div class="cta-group">
                @auth
                    <a href="{{ $teamSlug ? route('dashboard', $teamSlug) : route('teams.index') }}" class="btn-primary">
                        Abrir mi comunidad &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary">
                        Iniciar sesión &rarr;
                    </a>
                    <a href="{{ route('register') }}" class="btn-icon">
                        <span>Crear mi cuenta</span>
                        <span class="btn-icon-arrow" aria-hidden="true">&rarr;</span>
                    </a>
                @endauth
            </div>

            <div class="feature-strip" aria-label="Ventajas de la comunidad">
                <div class="feature-pill">
                    <span class="feature-kicker">01</span>
                    <div>
                        <strong>Comunidades activas</strong>
                        <small>Grupos por juego y estilo de play</small>
                    </div>
                </div>
                <div class="feature-pill">
                    <span class="feature-kicker">02</span>
                    <div>
                        <strong>Noticias reales</strong>
                        <small>Actualizaciones y contenido verificado</small>
                    </div>
                </div>
                <div class="feature-pill">
                    <span class="feature-kicker">03</span>
                    <div>
                        <strong>Juego en equipo</strong>
                        <small>Más fácil encontrar squad y hablar</small>
                    </div>
                </div>
            </div>

            <div class="stats-card">
                <div class="stat-item">
                    <h3>{{ $formatCompactNumber($usuarios_activos ?? 0) }}</h3>
                    <p>USUARIOS ACTIVOS</p>
                </div>
                <div class="stat-item">
                    <h3>{{ $formatCompactNumber($post_diarios ?? 0) }}</h3>
                    <p>POST DIARIOS</p>
                </div>
                <div class="stat-item">
                    <h3>{{ rtrim(rtrim(number_format($telemetria ?? 0, 1, ',', '.'), '0'), ',') }}%</h3>
                    <p>TELEMETRÍA</p>
                </div>
            </div>
        </section>

        <section class="hero-right news-carousel" aria-label="Noticias oficiales destacadas" data-news-carousel>
            <div class="slider-header">
                <div>
                    <span class="badge-tag">NEXUS // ACTUALIZACIONES</span>
                    <p class="carousel-kicker">Noticias oficiales</p>
                </div>
                @if ($officialNews->count() > 1)
                    <div class="slider-controls">
                        <button type="button" class="control-btn" data-carousel-prev aria-label="Noticia anterior">&larr;</button>
                        <button type="button" class="control-btn" data-carousel-next aria-label="Siguiente noticia">&rarr;</button>
                    </div>
                @endif
            </div>

            <div class="carousel-stage">
                @forelse ($officialNews as $index => $noticia)
                    @php
                        $carouselImageUrl = null;

                        if (!empty($noticia->imagen_url)) {
                            $carouselImageUrl = filter_var($noticia->imagen_url, FILTER_VALIDATE_URL)
                                ? $noticia->imagen_url
                                : \Illuminate\Support\Facades\Storage::disk('public')->url($noticia->imagen_url);
                        }

                        if (empty($carouselImageUrl)) {
                            $carouselImageUrl = asset('nexus.png');
                        }
                    @endphp

                    <article class="carousel-slide {{ $index === 0 ? 'is-active' : '' }}" data-carousel-slide>
                        <img src="{{ $carouselImageUrl }}" alt="{{ $noticia->titulo }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" onerror="this.onerror=null;this.src='{{ asset('nexus.png') }}';">
                        <div class="carousel-slide-overlay">
                            <p class="official-news-source">{{ $noticia->fuente_nombre ?: 'NEXUS' }} · {{ $noticia->created_at?->diffForHumans() }}</p>
                            <h2>{{ $noticia->titulo }}</h2>
                            <p>{{ $noticia->contenido }}</p>
                            <a href="{{ $noticia->fuente_url }}" target="_blank" rel="noopener noreferrer" class="official-news-link">Leer fuente original &rarr;</a>
                        </div>
                    </article>
                @empty
                    <div class="carousel-empty">
                        <span class="badge-tag">NEXUS // ACTUALIZACIONES</span>
                        <h2>Noticias oficiales</h2>
                        <p>Las noticias aparecerán aquí cuando se ejecute la sincronización.</p>
                    </div>
                @endforelse
            </div>

            @if ($officialNews->count() > 1)
                <div class="carousel-dots" aria-label="Seleccionar noticia">
                    @foreach ($officialNews as $index => $noticia)
                        <button type="button" class="carousel-dot {{ $index === 0 ? 'is-active' : '' }}" data-carousel-dot="{{ $index }}" aria-label="Mostrar noticia {{ $index + 1 }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}"></button>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    @vite('resources/js/home.js')
    </div>
</x-layouts::public>