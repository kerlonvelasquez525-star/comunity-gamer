<x-layouts::guest :title="__('Nexus Community')">
    @auth
        <livewire:pages::teams.pending-invitations-modal />
    @endauth

    <link rel="stylesheet" href="{{ asset('css/style_th.css') }}">

    {{-- NAVBAR SUPERIOR --}}
    <header>
        <div class="brand">
            <div class="brand-logo"><img src="{{ asset('nexus.png') }}" alt="Nexus Community Logo"></div>
            <span class="brand-title">nexus-comunity</span>
        </div>

        <nav>
            <a href="#" class="active">home</a>
            <a href="#">noticias</a>
            <a href="#">comentarios</a>
            <a href="#">comunidad</a>
            <a href="#">problemas</a>
        </nav>

        <div class="header-right">
            <div class="sys-status">
                <span class="status-dot"></span>
            </div>
            <a href="{{ route('login') }}" class="btn-signin">
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
                GameVault es tu banco de memoria externo para conocer tus juegos.
            </p>

            <div class="cta-group">
                <a href="{{ route('login') }}" class="btn-primary">
                    Iniciar sesión &rarr;
                </a>
                <a href="{{ route('register') }}" class="btn-icon">
                    registrarse &gt;
                </a>
            </div>

            {{-- TARJETA DE ESTADÍSTICAS REUBICADA --}}
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
                    <div class="squad-item">
                        <div class="squad-info">
                            <h4>BUGS NUEVOS</h4>
                            <p>Cyberpunk / Extraction</p>
                        </div>
                        <button class="btn-join">UNIRSE</button>
                    </div>

                    <div class="squad-item">
                        <div class="squad-info">
                            <h4>BUSCANDO SQUADS</h4>
                            <p>FORTNITE, CSGO, COD • 4-5 jugadores</p>
                        </div>
                        <button class="btn-join">UNIRSE</button>
                    </div>
                </div>
            </div>
        </section>

        {{-- COLUMNA DERECHA: SLIDER DE JUEGOS --}}
        <section class="hero-right">
            <div class="slider-header">
                <h2 class="slider-title">EXPLORAR BASES DE DATOS</h2>
                <div class="slider-controls">
                    <button id="slider-up" class="control-btn" aria-label="Subir">&lt;</button>
                    <button id="slider-down" class="control-btn" aria-label="Bajar">&gt;</button>
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
                        'badge' => 'ANÁLISIS DE JUEGO', 'read' =>
    '5 MIN READ',
                        'title' => 'SHADOW REALMS: Estrategias de sigilo y combate',
                        'desc'  => 'Exploración de las mecánicas de sigilo, rutas de escape y optimización de recursos en entornos urbanos.',
                        'user'  => 'StealthMaster', 'time' => 'HACE 3H'],
                    ['img' => 'https://images.unsplash.com/photo-1581091870620-1c8e5f4b6f1d?auto=format&fit=crop&w=800&q=80',
                        'badge' => 'NOVEDADES DE JUEGO', 'read' =>
    '7 MIN READ',
                        'title' => 'CYBER HORIZON: Explorando la expansión de mundo abierto',
                        'desc'  => 'Análisis de la nueva expansión, incluyendo misiones secundarias         y la integración de la inteligencia artificial en NPCs.',
                        'user'  => 'CyberExplorer', 'time' => 'HACE 4H'],
                    ['img' => 'https://images.unsplash.com/photo-1593642634367-d91a135587b5?auto=format&fit=crop&w=800&q=80',
                        'badge' => 'ACTUALIZACIÓN DE MOTOR', 'read' =>
    '8 MIN READ',
                        'title' => 'VIRTUAL REALITY: Mejoras en la física y la interacción',
                        'desc'  => 'Revisión de las últimas mejoras en el motor de realidad virtual, incluyendo la simulación de físicas y la respuesta háptica.',
                        'user'  => 'VRTechie', 'time' => 'HACE 6H'],
                ] as $game)
                    <article class="game-card">
                        <img src="{{ $game['img'] }}" alt="{{ $game['title'] }}" class="game-image">
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

    <script src="{{ asset('js/home.js') }}"></script>
</x-layouts::guest>