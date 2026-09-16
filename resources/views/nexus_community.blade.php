@php
    $teamSlug = auth()->user()?->currentTeam?->slug;

    $navLinks = [
        ['label' => 'home',        'route' => 'dashboard'],
        ['label' => 'noticias',    'route' => 'noticias.index'],
        ['label' => 'comentarios', 'route' => 'comentarios.index'],
        ['label' => 'comunidad',   'route' => 'comunidad.index'],
        ['label' => 'problemas',   'route' => 'problemas.index'],
    ];

    // Array de tarjetas para el slider de la derecha
    $gamesCards = [
        [
            'img'   => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=800&q=80',
            'badge' => 'ÚLTIMAS NOTICIAS', 
            'read'  => '4 MIN READ',
            'title' => 'PROYECTO BLACKOUT: Descifrando las nuevas reglas de extracción en los videojuegos',
            'desc'  => 'Un análisis exhaustivo de las mecánicas del parche v4.12, las nubes de radiación dinámicas y las rutas tácticas óptimas de despliegue.',
            'user'  => 'GhostOperator', 
            'time'  => 'HACE 2H'
        ],
        [
            'img'   => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=800&q=80',
            'badge' => 'ÚLTIMOS PARCHES', 
            'read'  => '6 MIN READ',
            'title' => 'NEON VELOCITY: Actualización de motor de aceleración',
            'desc'  => 'Revisión completa de la física de derrape en circuitos urbanos y enlaces cibernéticos nivel 3.',
            'user'  => 'ViperNet', 
            'time'  => 'HACE 5H'
        ],
        [
            'img'   => 'https://images.unsplash.com/photo-1605902711622-cfb43c443f6c?auto=format&fit=crop&w=800&q=80',
            'badge' => 'ANÁLISIS DE JUEGO', 
            'read'  => '5 MIN READ',
            'title' => 'SHADOW REALMS: Estrategias de sigilo y combate',
            'desc'  => 'Exploración de las mecánicas de sigilo, rutas de escape y optimización de recursos en entornos urbanos.',
            'user'  => 'StealthMaster', 
            'time'  => 'HACE 3H'
        ],
        [
            'img'   => 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?auto=format&fit=crop&w=800&q=80',
            'badge' => 'NOVEDADES DE JUEGO', 
            'read'  => '7 MIN READ',
            'title' => 'CYBER HORIZON: Explorando la expansión de mundo abierto',
            'desc'  => 'Análisis de la nueva expansión, incluyendo misiones secundarias y la integración de la inteligencia artificial en NPCs.',
            'user'  => 'CyberExplorer', 
            'time'  => 'HACE 4H'
        ],
        [
            'img'   => 'https://images.unsplash.com/photo-1593642634367-d91a135587b5?auto=format&fit=crop&w=800&q=80',
            'badge' => 'ACTUALIZACIÓN DE MOTOR', 
            'read'  => '8 MIN READ',
            'title' => 'VIRTUAL REALITY: Mejoras en la física y la interacción',
            'desc'  => 'Revisión de las últimas mejoras en el motor de realidad virtual, incluyendo la simulación de físicas y la respuesta háptica.',
            'user'  => 'VRTechie', 
            'time'  => 'HACE 6H'
        ],
    ];
@endphp

{{-- Usamos :: para el layout (namespace de la carpeta /resources/views/layouts) --}}
<x-layouts::public :title="__('Nexus Community')">
    <div class="public-home">
    
    <link rel="stylesheet" href="{{ asset('css/style_th.css') }}">

    {{-- Usamos punto . para el componente de Livewire --}}
    <livewire:pages.teams.pending-invitations-modal />

    {{-- NAVBAR SUPERIOR --}}
    <header>
        <div class="brand">
            <div class="brand-logo">
                <img src="{{ asset('nexus.png') }}" alt="Nexus Community" width="48" height="48" decoding="async">
            </div>
            <span class="brand-title">nexus-comunity</span>
        </div>

        <nav aria-label="Navegación principal">
            @foreach ($navLinks as $index => $link)
                @auth
                    @if ($teamSlug)
                        {{-- Logueado y con equipo activo: va directo a la sección --}}
                        <a href="{{ route($link['route'], $teamSlug) }}" @class(['active' => $index === 0])>
                            {{ $link['label'] }}
                        </a>
                    @else
                        {{-- Logueado pero SIN equipo activo: llévalo a elegir/crear equipo, no a login --}}
                        <a href="{{ route('teams.index') }}" @class(['active' => $index === 0])>
                            {{ $link['label'] }}
                        </a>
                    @endif
                @else
                    {{-- Sin sesión: la sección exige autenticación --}}
                    <a href="{{ route('login') }}" @class(['active' => $index === 0])>
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
               class="btn-signin"
               aria-label="{{ $teamSlug ? 'Ir al panel' : (auth()->check() ? 'Elegir equipo' : 'Iniciar sesión') }}">
                <flux:icon name="user" class="w-4 h-4" />
            </a>
        </div>
    </header>

    {{-- ESTRUCTURA PRINCIPAL DEL HERO --}}
    <div class="hero-container">

        {{-- COLUMNA IZQUIERDA --}}
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

            {{-- TARJETA DE ESTADÍSTICAS --}}
            <div class="stats-card">
                <div class="stat-item">
                    <h3>248K</h3>
                    <p>USUARIOS ACTIVOS</p>
                </div>
                <div class="stat-item">
                    <h3>12.4M</h3>
                    <p>POST DIARIOS</p>
                </div>
                <div class="stat-item">
                    <h3>98.4%</h3>
                    <p>TELEMETRÍA</p>
                </div>
            </div>

            {{-- SALAS DE CONVERSACIÓN ACTIVAS --}}
            <div class="extra-section">
                <div class="extra-header">
                    <span class="extra-title">Salas activas ahora</span>
                    <span class="voice-tag">Explorar por juego</span>
                </div>

                <div class="squad-list">
                    @foreach ([
                        ['name' => 'BUGS NUEVOS',   'detail' => 'Cyberpunk / Extraction'],
                        ['name' => 'BUSCANDO SQUADS',  'detail' => 'FORTNITE, CSGO, COD • 4-5 jugadores'],
                    ] as $squad)
                        <div class="squad-item">
                            <div class="squad-info">
                                <h4>{{ $squad['name'] }}</h4>
                                <p>{{ $squad['detail'] }}</p>
                            </div>
                            <a href="{{ $teamSlug ? route('comunidad.index', $teamSlug) : (auth()->check() ? route('teams.index') : route('login')) }}"
                               class="btn-join">UNIRSE</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- COLUMNA DERECHA: SLIDER DE JUEGOS --}}
        <section class="hero-right">
            <div class="slider-header">
                <h2 class="slider-title">Historias para descubrir</h2>
                <div class="slider-controls">
                    <button type="button" id="slider-up" class="control-btn" aria-label="Anterior">&lt;</button>
                    <button type="button" id="slider-down" class="control-btn" aria-label="Siguiente">&gt;</button>
                </div>
            </div>

            <div class="games-slider" id="slider">
                @foreach ($gamesCards as $game)
                    <article class="game-card">
                        <img src="{{ $game['img'] }}" alt="" loading="lazy" decoding="async" class="game-image">
                        <div class="game-card-body">
                            <div>
                                <span class="news-badge">{{ $game['badge'] }}</span>
                                <span class="read-time">{{ $game['read'] }}</span>
                            </div>
                            <h3 class="game-card-title">{{ $game['title'] }}</h3>
                            <p class="game-card-desc">{{ $game['desc'] }}</p>
                            <div class="game-card-footer">
                                <div class="operator-info">
                                    <div class="avatar-sm"></div>
                                    <span class="operator-name">{{ $game['user'] }}</span>
                                </div>
                                <span class="deploy-time">PUBLICADO: {{ $game['time'] }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

    </div>

    <section class="official-news-section" aria-labelledby="official-news-title">
        <div class="official-news-heading">
            <span class="badge-tag">FUENTES VERIFICADAS // ACTUALIZACIONES</span>
            <h2 id="official-news-title" class="official-news-title">Noticias oficiales de videojuegos</h2>
            <p class="hero-description">Novedades publicadas por los blogs oficiales de las principales plataformas.</p>
        </div>

        <div class="official-news-grid">
            @forelse ($officialNews as $noticia)
                <article class="official-news-card" id="noticia-{{ $noticia->id }}">
                    @if ($noticia->imagen_url)
                        <img src="{{ filter_var($noticia->imagen_url, FILTER_VALIDATE_URL) ? $noticia->imagen_url : \Illuminate\Support\Facades\Storage::disk('public')->url($noticia->imagen_url) }}" alt="{{ $noticia->titulo }}" class="official-news-image" loading="lazy">
                    @endif
                    <div class="official-news-content">
                        <p class="official-news-source">{{ $noticia->fuente_nombre }} · {{ $noticia->created_at?->diffForHumans() }}</p>
                        <h3>{{ $noticia->titulo }}</h3>
                        <p class="official-news-description">{{ $noticia->contenido }}</p>
                        <a href="{{ $noticia->fuente_url }}" target="_blank" rel="noopener noreferrer" class="official-news-link">Leer fuente original &rarr;</a>

                        <div class="official-comments">
                            <h4>Comentarios ({{ $noticia->comentarios->count() }})</h4>
                            @forelse ($noticia->comentarios->take(3) as $comentario)
                                <p class="official-comment"><strong>{{ $comentario->autor?->name ?? 'Usuario' }}:</strong> {{ $comentario->contenido }}</p>
                            @empty
                                <p class="official-comment-empty">Sé el primero en comentar.</p>
                            @endforelse

                            @auth
                                <form method="POST" action="{{ route('official-news.comments.store', $noticia) }}" class="official-comment-form">
                                    @csrf
                                    <label class="sr-only" for="comment-{{ $noticia->id }}">Escribe un comentario</label>
                                    <input id="comment-{{ $noticia->id }}" name="contenido" maxlength="2000" required placeholder="Escribe un comentario...">
                                    <button type="submit">Comentar</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="official-news-link">Inicia sesión para comentar</a>
                            @endauth
                        </div>
                    </div>
                </article>
            @empty
                <p class="official-news-empty">Las noticias oficiales aparecerán aquí cuando se ejecute la sincronización.</p>
            @endforelse
        </div>
    </section>

    <script src="{{ asset('js/home.js') }}" defer></script>
    </div>
</x-layouts::public>