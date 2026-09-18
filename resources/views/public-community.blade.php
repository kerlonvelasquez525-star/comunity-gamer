<x-layouts::public :title="__('Comunidades activas')">
    <div class="public-home">
        @vite('resources/css/style_th.css')

        <header class="community-public-header">
            <div class="brand">
                <div class="brand-logo">
                    <img src="{{ asset('nexus.png') }}" alt="Nexus Community" width="48" height="48" decoding="async">
                </div>
                <span class="brand-title">nexus-comunity</span>
            </div>

            <nav aria-label="Navegación pública" class="community-public-nav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('public-comunidad.index') }}" class="active">Comunidad</a>
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
                            <img
                                src="{{ asset('nexus.png') }}"
                                alt="{{ $comunidad->nombre }}"
                                class="official-news-image"
                                loading="lazy"
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
