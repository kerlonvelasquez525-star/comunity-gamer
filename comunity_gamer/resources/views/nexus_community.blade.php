<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head', ['title' => __('Nexus Community')])
    <link rel="stylesheet" href="{{ asset('css/style_th.css') }}">
</head>
<body>

    {{-- HERO --}}
    <div class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 lg:px-8">

        <div class="grid gap-6 lg:grid-cols-2">

            {{-- COLUMNA IZQUIERDA --}}
            <section class="hero-left flex flex-col gap-4">
                <flux:badge color="lime">BIENVENIDO // NUEVOS JUEGOS CADA DÍA</flux:badge>

                <flux:heading size="xl" level="1">CONOCE TUS JUEGOS</flux:heading>

                <flux:text>
                    Reseñas tácticas, datos de esports en bruto y publicaciones de la
                    comunidad sin filtros. Nexus Community es tu banco de memoria externo.
                </flux:text>

                <div class="flex gap-3">
                    <flux:button href="{{ route('login') }}" variant="primary" icon-trailing="arrow-right">
                        Iniciar sesión
                    </flux:button>
                    <flux:button href="{{ route('register') }}" icon-trailing="user-plus">
                        Registrarse
                    </flux:button>
                </div>

                {{-- MÉTRICAS (mismo patrón grid de la imagen: md:grid-cols-3) --}}
                <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                    <flux:card class="text-center">
                        <flux:heading size="lg">248K</flux:heading>
                        <flux:text>USUARIOS ACTIVOS</flux:text>
                    </flux:card>
                    <flux:card class="text-center">
                        <flux:heading size="lg">12.4M</flux:heading>
                        <flux:text>POST DIARIOS</flux:text>
                    </flux:card>
                    <flux:card class="text-center">
                        <flux:heading size="lg">98.4%</flux:heading>
                        <flux:text>TELEMETRÍA</flux:text>
                    </flux:card>
                </div>

                {{-- SALAS ACTIVAS --}}
                <flux:card>
                    <div class="mb-3 flex items-center justify-between">
                        <flux:heading size="sm">⚡ SALAS DE CONVERSACIÓN ACTIVAS</flux:heading>
                        <flux:badge size="sm">FILTRAR POR JUEGO</flux:badge>
                    </div>

                    <div class="flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <flux:heading size="sm">BUGS NUEVOS</flux:heading>
                                <flux:text>Cyberpunk / Extraction</flux:text>
                            </div>
                            <flux:button size="sm" variant="primary" class="shrink-0 px-4">UNIRSE</flux:button>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <flux:heading size="sm">BUSCANDO SQUADS</flux:heading>
                                <flux:text>FORTNITE, CSGO, COD • 4-5 jugadores</flux:text>
                            </div>
                            <flux:button size="sm" variant="primary" class="shrink-0 px-4">UNIRSE</flux:button>
                        </div>
                    </div>
                </flux:card>
            </section>

            {{-- COLUMNA DERECHA: SLIDER DE JUEGOS --}}
            <section class="hero-right flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <flux:heading size="sm">EXPLORAR BASES DE DATOS</flux:heading>
                    <div class="flex gap-2">
                        <flux:button id="slider-up" size="sm" icon="chevron-up" aria-label="Subir" />
                        <flux:button id="slider-down" size="sm" icon="chevron-down" aria-label="Bajar" />
                    </div>
                </div>

                <div class="games-slider" id="slider">
                    @foreach ([
                        ['img' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=800&q=80',
                         'badge' => 'ÚLTIMAS NOTICIAS', 'read' => '4 MIN READ',
                         'title' => 'PROYECTO BLACKOUT: Descifrando las nuevas reglas de extracción',
                         'desc'  => 'Análisis exhaustivo del parche v4.12, nubes de radiación dinámicas y rutas tácticas óptimas.',
                         'user'  => 'GhostOperator', 'time' => 'HACE 2H'],
                        ['img' => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=800&q=80',
                         'badge' => 'ÚLTIMOS PARCHES', 'read' => '6 MIN READ',
                         'title' => 'NEON VELOCITY: Actualización de motor de aceleración',
                         'desc'  => 'Revisión completa de la física de derrape en circuitos urbanos y enlaces cibernéticos nivel 3.',
                         'user'  => 'ViperNet', 'time' => 'HACE 5H'],
                        ['img' => 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?auto=format&fit=crop&w=800&q=80',
                         'badge' => 'FINAL DEL TORNEO', 'read' => '3 MIN READ',
                         'title' => 'CYBER PROTOCOL: Clasificatorias globales de la temporada 5',
                         'desc'  => 'Los mejores equipos de la división Cyber, estadísticas y fechas de las finales.',
                         'user'  => 'NovaAdmin', 'time' => 'HACE 1D'],
                    ] as $game)
                        <article class="game-card">
                            <img src="{{ $game['img'] }}" alt="{{ $game['title'] }}" class="game-image">
                            <div class="game-card-body">
                                <div class="flex items-center gap-2">
                                    <flux:badge size="sm">{{ $game['badge'] }}</flux:badge>
                                    <flux:text class="read-time">{{ $game['read'] }}</flux:text>
                                </div>
                                <flux:heading size="md">{{ $game['title'] }}</flux:heading>
                                <flux:text>{{ $game['desc'] }}</flux:text>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <flux:avatar size="xs" :name="$game['user']" />
                                        <flux:text>{{ $game['user'] }}</flux:text>
                                    </div>
                                    <flux:text>PUBLICADO: {{ $game['time'] }}</flux:text>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        </div>
    </div>

    <script src="{{ asset('js/home.js') }}"></script>
    @fluxScripts
</body>
</html>