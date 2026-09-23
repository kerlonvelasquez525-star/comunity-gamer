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
                            <div class="official-comments" data-news-comments>
                                <div class="official-comments-heading">
                                    <h4>Conversación</h4>
                                    <span data-comments-total>{{ $noticia->comentarios->count() }}</span>
                                </div>
                                <div class="official-comment-list" data-comments-list>
                                    @forelse ($noticia->comentarios as $comentario)
                                        <p class="official-comment">
                                            <span class="official-comment-header">
                                                <strong>{{ $comentario->autor?->name ?? 'Usuario' }}</strong>
                                                @if (auth()->check() && $comentario->user_id === auth()->id())
                                                    <span class="official-comment-actions">
                                                        <button type="submit" form="public-delete-comment-{{ $comentario->id }}">Eliminar</button>
                                                    </span>
                                                @endif
                                            </span>
                                            <span>{{ $comentario->contenido }}</span>
                                            @if (auth()->check() && $comentario->user_id === auth()->id())
                                                <form method="POST" action="{{ route('official-news.comments.update', [$noticia, $comentario]) }}" class="official-comment-edit-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="text" name="contenido" value="{{ $comentario->contenido }}" maxlength="2000" aria-label="Editar comentario" required>
                                                    <button type="submit">Guardar</button>
                                                </form>
                                                <form id="public-delete-comment-{{ $comentario->id }}" method="POST" action="{{ route('official-news.comments.destroy', [$noticia, $comentario]) }}" class="hidden">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @endif
                                        </p>
                                    @empty
                                        <p class="official-comment-empty">Sé el primero en comentar.</p>
                                    @endforelse
                                </div>
                                <form method="POST" action="{{ route('official-news.comments.store', $noticia) }}" class="official-comment-form" data-public-comment-form data-authenticated="{{ auth()->check() ? 'true' : 'false' }}">
                                    @csrf
                                    <input type="text" name="contenido" maxlength="2000" placeholder="Escribe una respuesta..." aria-label="Escribe una respuesta" required>
                                    <button type="submit">Comentar</button>
                                </form>
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

        <div class="public-auth-modal" data-public-auth-modal hidden>
            <div class="public-auth-modal-backdrop" data-public-auth-close></div>
            <section class="public-auth-dialog" role="dialog" aria-modal="true" aria-labelledby="public-auth-title">
                <button type="button" class="public-auth-close" data-public-auth-close aria-label="Cerrar">&times;</button>
                <span class="badge-tag">NEXUS // CUENTA NECESARIA</span>
                <h2 id="public-auth-title">Regístrate para participar</h2>
                <p> Puedes leer toda la conversación sin iniciar sesión. Para guardar tu respuesta en esta noticia, crea tu cuenta o inicia sesión.</p>
                <div class="public-auth-actions">
                    <a href="{{ route('register') }}" class="btn-primary">Crear mi cuenta &rarr;</a>
                    <a href="{{ route('login') }}" class="btn-icon">Iniciar sesión</a>
                </div>
            </section>
        </div>
    </div>

    @vite('resources/js/home.js')
</x-layouts::public>
