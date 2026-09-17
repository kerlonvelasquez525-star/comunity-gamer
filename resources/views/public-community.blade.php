<x-layouts::public :title="__('Comunidades activas')">
    <div class="public-home">
        <link rel="stylesheet" href="{{ asset('css/style_th.css') }}">

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

            <div class="official-news-grid community-public-grid">
                @forelse ($items as $comunidad)
                    <article class="official-news-card community-public-card">
                        <div class="official-news-content">
                            <p class="official-news-source">{{ $comunidad->creador?->name ?? 'Miembro de la comunidad' }}</p>
                            <h3>{{ $comunidad->nombre }}</h3>
                            <p class="official-news-description">{{ $comunidad->descripcion ?: 'Una nueva comunidad gamer, todavía sin descripción.' }}</p>
                            <div class="community-meta-row">
                                <span class="community-member-count">
                                    {{ $comunidad->miembros_count }} {{ $comunidad->miembros_count === 1 ? 'miembro' : 'miembros' }}
                                </span>
                                <span class="community-status-pill">Activa</span>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="official-news-empty community-empty-state">Todavía no hay comunidades públicas disponibles.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts::public>
