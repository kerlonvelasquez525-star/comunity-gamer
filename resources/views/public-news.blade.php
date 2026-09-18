<x-layouts::public :title="__('Noticias oficiales')">
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
                <a href="{{ route('public-noticias.index') }}" class="active">Noticias</a>
                <a href="{{ route('public-comunidad.index') }}">Comunidad</a>
                <a href="{{ route('public-problemas.index') }}">Soporte</a>
            </nav>
        </header>

        <section class="official-news-section community-public-section" aria-labelledby="news-title">
            <div class="official-news-heading community-public-heading">
                <span class="badge-tag">NEXUS // NOTICIAS OFICIALES</span>
                <h1 id="news-title" class="official-news-title">Noticias oficiales</h1>
                <p class="hero-description">Mantente al día con lanzamientos, eventos y actualizaciones del mundo gamer sin necesidad de iniciar sesión.</p>
            </div>

            <div class="official-news-grid community-public-grid">
                @forelse ($items as $noticia)
                    @php
                        $newsImageUrl = !empty($noticia->imagen_url)
                            ? (filter_var($noticia->imagen_url, FILTER_VALIDATE_URL) ? $noticia->imagen_url : \Illuminate\Support\Facades\Storage::disk('public')->url($noticia->imagen_url))
                            : asset('nexus.png');
                    @endphp

                    <article class="official-news-card news-public-card">
                        <div class="news-public-image-wrap">
                            <img src="{{ $newsImageUrl }}" alt="{{ $noticia->titulo }}" class="official-news-image" loading="lazy">
                            <span class="news-public-badge">{{ strtoupper($noticia->fuente_nombre ?: 'NEXUS') }}</span>
                        </div>
                        <div class="official-news-content">
                            <p class="official-news-source">{{ $noticia->fuente_nombre }} · {{ $noticia->created_at?->diffForHumans() }}</p>
                            <h3>{{ $noticia->titulo }}</h3>
                            <p class="official-news-description">{{ $noticia->contenido }}</p>
                            <div class="community-meta-row">
                                <span class="community-member-count">{{ $noticia->comentarios->count() }} comentarios</span>
                                <a href="{{ $noticia->fuente_url ?? route('home') }}" target="_blank" rel="noopener noreferrer" class="official-news-link">Leer &rarr;</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="official-news-empty community-empty-state">Todavía no hay noticias oficiales públicas.</p>
                @endforelse
            </div>

            @if ($items->hasPages())
                <div class="community-pagination">{{ $items->links() }}</div>
            @endif
        </section>
    </div>
</x-layouts::public>
