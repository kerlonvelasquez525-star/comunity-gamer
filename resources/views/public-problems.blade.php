<x-layouts::public :title="__('Soporte y ayuda')">
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
                <a href="{{ route('public-noticias.index') }}">Noticias</a>
                <a href="{{ route('public-comunidad.index') }}">Comunidad</a>
                <a href="{{ route('public-problemas.index') }}" class="active">Soporte</a>
            </nav>
        </header>

        <section class="official-news-section community-public-section" aria-labelledby="support-title">
            <div class="official-news-heading community-public-heading">
                <span class="badge-tag">SOPORTE // AYUDA</span>
                <h1 id="support-title" class="official-news-title">Soporte y ayuda</h1>
                <p class="hero-description">Consulta información útil, descubre guías rápidas y revisa recursos para el mundo gamer sin necesidad de entrar con cuenta.</p>
            </div>

            <div class="official-news-grid community-public-grid">
                @foreach ($helpPoints as $point)
                    <article class="official-news-card news-public-card">
                        <div class="official-news-content">
                            <p class="official-news-source">Ayuda rápida</p>
                            <h3>Recurso útil</h3>
                            <p class="official-news-description">{{ $point }}</p>
                            <a href="{{ route('home') }}" class="official-news-link">Volver al inicio &rarr;</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts::public>
