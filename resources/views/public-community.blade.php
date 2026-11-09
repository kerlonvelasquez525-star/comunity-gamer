<x-layouts::public :title="__('Comunidades activas')">
    <div class="public-home">
        @vite('resources/css/style_th.css')

        @php
            $teamSlug = auth()->user()?->currentTeam?->slug;
            $communityNavItems = [
                ['label' => 'Home', 'href' => route('home'), 'active' => false],
            ];

            if (auth()->check()) {
                $communityNavItems = array_merge($communityNavItems, [
                    [
                        'label' => 'Panel',
                        'href' => $teamSlug ? route('dashboard', $teamSlug) : route('teams.index'),
                        'active' => request()->routeIs('dashboard'),
                    ],
                    [
                        'label' => 'Noticias',
                        'href' => $teamSlug ? route('noticias.index', $teamSlug) : route('public-noticias.index'),
                        'active' => request()->routeIs('noticias.*', 'public-noticias.*'),
                    ],
                    [
                        'label' => 'Comunidades',
                        'href' => $teamSlug ? route('comunidad.index', $teamSlug) : route('public-comunidad.index'),
                        'active' => request()->routeIs('comunidad.*', 'public-comunidad.*'),
                    ],
                    [
                        'label' => 'Soporte',
                        'href' => $teamSlug ? route('problemas.index', $teamSlug) : route('public-problemas.index'),
                        'active' => request()->routeIs('problemas.*', 'public-problemas.*'),
                    ],
                    [
                        'label' => 'Asistente',
                        'href' => route('chatbot.index'),
                        'active' => request()->routeIs('chatbot.*'),
                    ],
                ]);
            } else {
                $communityNavItems = array_merge($communityNavItems, [
                    ['label' => 'Noticias', 'href' => route('public-noticias.index'), 'active' => false],
                    ['label' => 'Comunidad', 'href' => route('public-comunidad.index'), 'active' => true],
                    ['label' => 'Soporte', 'href' => route('public-problemas.index'), 'active' => false],
                ]);
            }

            $logoHref = route('home');
        @endphp

        <header class="community-public-header">
            <div class="brand">
                <a href="{{ $logoHref }}" class="brand-logo" aria-label="Ir al inicio">
                    <img src="{{ asset('nexus.png') }}" alt="Nexus Community" width="48" height="48" decoding="async">
                </a>
                <a href="{{ $logoHref }}" class="brand-title" aria-label="Ir al inicio">nexus-comunity</a>
            </div>

            <nav aria-label="Navegación pública" class="community-public-nav">
                @foreach ($communityNavItems as $item)
                    <a href="{{ $item['href'] }}" @class(['active' => $item['active']]) wire:navigate>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </header>

        <section class="official-news-section community-public-section" aria-labelledby="community-title">
            <div class="official-news-heading community-public-heading">
                <span class="badge-tag">COMUNIDAD // SQUADS ACTIVOS</span>
                <h1 id="community-title" class="official-news-title">Comunidades activas</h1>
                <p class="hero-description">Explora grupos de jugadores, descubre partidas y comparte intereses sin necesidad de registrarte.</p>
            </div>

            @if (session('status'))
                <div class="community-public-status" role="status">{{ session('status') }}</div>
            @endif

            <form method="GET" action="{{ route('public-comunidad.index') }}" class="community-public-filters">
                <input type="search" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar comunidad..." aria-label="Buscar comunidad">
                @foreach (['juego_principal' => 'Juego', 'plataforma' => 'Plataforma', 'region' => 'Región', 'idioma' => 'Idioma', 'modalidad' => 'Modalidad', 'horario' => 'Horario', 'tipo' => 'Estilo', 'nivel' => 'Nivel', 'rango' => 'Rango', 'estado' => 'Estado'] as $filter => $label)
                    <label>
                        <span>{{ $label }}</span>
                        <select name="{{ $filter }}">
                            <option value="">Todos</option>
                            @foreach ($filterOptions[$filter] ?? [] as $option)
                                <option value="{{ $option }}" @selected(request($filter) === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </label>
                @endforeach
                <button type="submit">Filtrar</button>
                @if (request()->hasAny(['buscar', 'juego_principal', 'plataforma', 'region', 'idioma', 'modalidad', 'horario', 'tipo', 'nivel', 'rango', 'estado']))
                    <a href="{{ route('public-comunidad.index') }}">Limpiar</a>
                @endif
            </form>

            <div class="official-news-grid community-public-grid">
                @forelse ($items as $comunidad)
                    <article class="official-news-card community-public-card">
                        <div class="news-public-image-wrap">
                            @php
                                $communityImage = ! empty($comunidad->imagen_url)
                                    ? (filter_var($comunidad->imagen_url, FILTER_VALIDATE_URL)
                                        ? $comunidad->imagen_url
                                        : \Illuminate\Support\Facades\Storage::disk('public')->url($comunidad->imagen_url))
                                    : asset('nexus.png');
                            @endphp
                            <img
                                src="{{ $communityImage }}"
                                alt="{{ $comunidad->nombre }}"
                                class="official-news-image"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='{{ asset('nexus.png') }}';"
                            >
                            <span class="news-public-badge">COMUNIDAD ACTIVA</span>
                        </div>
                        <div class="official-news-content">
                            <p class="official-news-source">{{ $comunidad->creador?->name ?? 'Miembro de la comunidad' }}</p>
                            <h3>{{ $comunidad->nombre }}</h3>
                            <p class="official-news-description">{{ $comunidad->descripcion ?: 'Una nueva comunidad gamer, todavía sin descripción.' }}</p>
                            <div class="community-filter-tags">
                                @foreach ([$comunidad->juego_principal, $comunidad->plataforma, $comunidad->region, $comunidad->modalidad, $comunidad->horario] as $tag)
                                    @if (filled($tag))
                                        <span>{{ $tag }}</span>
                                    @endif
                                @endforeach
                            </div>
                            <div class="community-meta-row">
                                <span class="community-member-count">
                                    {{ $comunidad->miembros_count }} {{ $comunidad->miembros_count === 1 ? 'miembro' : 'miembros' }}
                                </span>
                                <span class="community-status-pill">Activa</span>
                            </div>
                            @php
                                $membership = auth()->check() ? $comunidad->miembros->first() : null;
                                $application = auth()->check() ? $comunidad->solicitudes->first() : null;
                            @endphp
                            @if (! auth()->check())
                                <a href="{{ route('login') }}" class="official-news-link">Iniciar sesión para solicitar ingreso &rarr;</a>
                            @elseif ($membership)
                                <span class="community-join-state is-accepted">Ya perteneces a esta comunidad</span>
                            @elseif ($application?->estado === 'pendiente')
                                <span class="community-join-state">Solicitud pendiente de aprobación</span>
                            @elseif ($application?->estado === 'rechazada')
                                <form method="POST" action="{{ route('public-comunidad.apply', $comunidad) }}">
                                    @csrf
                                    <button type="submit" class="official-news-link community-join-button">Solicitar nuevamente &rarr;</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('public-comunidad.apply', $comunidad) }}">
                                    @csrf
                                    <button type="submit" class="official-news-link community-join-button">Solicitar ingreso &rarr;</button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <p class="official-news-empty community-empty-state">Todavía no hay comunidades públicas disponibles.</p>
                @endforelse
            </div>

            @if ($items->hasPages())
                <div class="community-pagination">{{ $items->links() }}</div>
            @endif
        </section>
    </div>
</x-layouts::public>
