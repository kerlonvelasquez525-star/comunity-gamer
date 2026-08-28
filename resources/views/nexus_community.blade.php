@php
    $teamSlug = auth()->user()?->currentTeam?->slug;

    $navLinks = [
        ['label' => 'home',        'route' => 'dashboard'],
        ['label' => 'noticias',    'route' => 'noticias.index'],
        ['label' => 'comentarios', 'route' => 'comentarios.index'],
        ['label' => 'comunidad',   'route' => 'comunidad.index'],
        ['label' => 'problemas',   'route' => 'problemas.index'],
    ];
@endphp

{{-- Usamos :: para el layout (namespace de la carpeta /resources/views/layouts) --}}
<x-layouts::public :title="__('Nexus Community')">
   
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
            <span class="badge-tag">BIENVENIDO // NUEVOS JUEGOS CADA DÍA</span>

            <h1 class="hero-title">CONOCE TUS JUEGOS</h1>

            <p class="hero-description">
                Reseñas tácticas, datos de esports en bruto y publicaciones de la comunidad sin filtros.
                Nexus Community es tu banco de memoria externo para conocer tus juegos.
            </p>

            <div class="cta-group">
                @auth
                    <a href="{{ $teamSlug ? route('dashboard', $teamSlug) : route('teams.index') }}" class="btn-primary">
                        Ir al panel &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary">
                        Iniciar sesión &rarr;
                    </a>
                    <a href="{{ route('register') }}" class="btn-icon">
                        registrarse &gt;
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
                    <span class="extra-title">⚡ SALAS DE CONVERSACION ACTIVAS</span>
                    <span class="voice-tag">FILTRAR POR JUEGO</span>
                </div>

                <div class="squad-list">
                    @foreach ([
                        ['name' => 'BUGS NUEVOS',      'detail' => 'Cyberpunk / Extraction'],
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
                <h2 class="slider-title">EXPLORAR BASES DE DATOS</h2>
                <div class="slider-controls">
                    <button type="button" id="slider-up" class="control-btn" aria-label="Anterior">&lt;</button>
                    <button type="button" id="slider-down" class="control-btn" aria-label="Siguiente">&gt;</button>
                </div>
            </div>

            <div class="games-slider" id="slider">
                @foreach ([
                    ['img' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=800&q=80',
                     'badge' => 'ÚLTIMAS NOTICIAS', 'read' => '4 MIN READ',
                     'title' => 'PROYECTO BLACKOUT: Descifrando las nuevas reglas de extracción en los videojuegos',
                     'desc'  => 'Un análisis exhaustivo de las mecánicas del parche v4.12, las nubes de radiación dinámicas y las rutas tácticas óptimas de despliegue.',
                     'user'  => 'GhostOperator', 'time' => 'HACE 2H'],
                    ['img' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=800&q=80',
                     'badge' => 'ÚLTIMOS PARCHES', 'read' => '6 MIN READ',
                     'title' => 'NEON VELOCITY: Actualización de motor de aceleración',
                     'desc'  => 'Revisión completa de la física de derrape en circuitos urbanos y enlaces cibernéticos nivel 3.',
                     'user'  => 'ViperNet', 'time' => 'HACE 5H'],
                    ['img' => 'https://images.unsplash.com/photo-1605902711622-cfb43c443f6c?auto=format&fit=crop&w=800&q=80',
                     'badge' => 'ANÁLISIS DE JUEGO', 'read' => '5 MIN READ',
                     'title' => 'SHADOW REALMS: Estrategias de sigilo y combate',
                     'desc'  => 'Exploración de las mecánicas de sigilo, rutas de escape y optimización de recursos en entornos urbanos.',
                     'user'  => 'StealthMaster', 'time' => 'HACE 3H'],
                    ['img' => 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?auto=format&fit=crop&w=800&q=80',
                     'badge' => 'NOVEDADES DE JUEGO', 'read' => '7 MIN READ',
                     'title' => 'CYBER HORIZON: Explorando la expansión de mundo abierto',
                     'desc'  => 'Análisis de la nueva expansión, incluyendo misiones secundarias y la integración de la inteligencia artificial en NPCs.',
                     'user'  => 'CyberExplorer', 'time' => 'HACE 4H'],
                    ['img' => 'https://images.unsplash.com/photo-1593642634367-d91a135587b5?auto=format&fit=crop&w=800&q=80',
                     'badge' => 'ACTUALIZACIÓN DE MOTOR', 'read' => '8 MIN READ',
                     'title' => 'VIRTUAL REALITY: Mejoras en la física y la interacción',
                     'desc'  => 'Revisión de las últimas mejoras en el motor de realidad virtual, incluyendo la simulación de físicas y la respuesta háptica.',
                     'user'  => 'VRTechie', 'time' => 'HACE 6H'],
                ] as $game)
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

    <script src="{{ asset('js/home.js') }}" defer></script>
</x-layouts::public>