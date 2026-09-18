
<x-layouts::app :title="__('Centro de Noticias')">
    @vite(['resources/css/noticias.css'])

    {{--
        Vista principal del módulo de noticias.
        Muestra el encabezado, filtros de búsqueda y la lista de noticias
        del equipo actual, además de permitir crear una nueva publicación.
    --}}
    <div class="news-page w-full space-y-8 p-6 lg:p-10">
        {{-- Encabezado con título del equipo y botón para publicar una noticia. --}}
        <header class="cyber-hero rounded-2xl border border-zinc-800/80 bg-zinc-900/95 p-8 text-white shadow-xl backdrop-blur-md">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-semibold text-amber-400">{{ $team->name ?? 'Equipo' }}</p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">Centro de noticias</h1>
                    <p class="mt-2 max-w-2xl text-sm text-zinc-400">Noticias de tu equipo y novedades de fuentes oficiales de videojuegos.</p>
                </div>
                @if (Route::has('noticias.create'))
                    <a href="{{ route('noticias.create', $team->slug) }}" wire:navigate class="inline-flex items-center justify-center rounded-xl bg-amber-400 px-5 py-3 text-sm font-bold text-zinc-950 transition hover:bg-amber-300">Compartir noticia</a>
                @endif
            </div>
        </header>
 
        {{-- Formulario de búsqueda y filtros para localizar noticias por texto o tipo. --}}
        <form method="GET" action="{{ route('noticias.index', $team->slug) }}" class="flex flex-col gap-3 rounded-xl border border-zinc-800 bg-zinc-900 p-4 sm:flex-row">
            <label class="sr-only" for="buscar">Buscar noticias</label>
            <input id="buscar" name="buscar" type="search" value="{{ request('buscar') }}" placeholder="Buscar noticias..." class="min-w-0 flex-1 rounded-lg border-zinc-700 bg-zinc-950 text-zinc-100 placeholder:text-zinc-500">
            <label class="flex items-center gap-2 text-sm text-zinc-400"><input type="checkbox" name="oficial" value="1" @checked(request()->boolean('oficial')) class="rounded border-zinc-700 bg-zinc-950 text-cyan-400"> Solo oficiales</label>
            <button type="submit" class="rounded-lg bg-cyan-400 px-4 py-2 font-semibold text-zinc-950 hover:bg-cyan-300">Buscar</button>
        </form>

        {{-- Grid principal donde se renderiza cada noticia como una tarjeta. --}}
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($items as $noticia)
                <article class="group flex flex-col overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 transition-all duration-300 hover:border-cyan-400/50 hover:shadow-lg hover:shadow-cyan-400/5">
                    @if ($noticia->imagen_url)
                        <div class="relative h-48 w-full overflow-hidden bg-zinc-950"><img src="{{ filter_var($noticia->imagen_url, FILTER_VALIDATE_URL) ? $noticia->imagen_url : \Illuminate\Support\Facades\Storage::disk('public')->url($noticia->imagen_url) }}" alt="{{ $noticia->titulo }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"></div>
                    @endif
                    <div class="flex flex-1 flex-col justify-between p-6">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-3 text-xs text-zinc-500"><span class="text-cyan-400">{{ $noticia->es_oficial ? 'FUENTE OFICIAL' : ($noticia->categoria ?: 'NOTICIA') }}</span><span class="font-mono text-zinc-400">{{ $noticia->created_at?->diffForHumans() }}</span></div>
                            <p class="text-xs font-semibold text-amber-500">Comunidad: {{ $noticia->equipo?->name ?? 'Comunidad general' }}</p>
                            <h2 class="line-clamp-2 text-lg font-bold text-zinc-100 group-hover:text-cyan-400">{{ $noticia->titulo }}</h2>
                            <p class="line-clamp-3 text-sm text-zinc-400">{{ $noticia->contenido }}</p>
                        </div>
                        <div class="mt-6 flex items-center justify-between border-t border-zinc-800/80 pt-4 text-xs">
                            <span class="font-medium text-zinc-300">Por <strong class="text-cyan-400">{{ $noticia->es_oficial ? $noticia->fuente_nombre : ($noticia->autor?->name ?? 'Autor desconocido') }}</strong></span>
                            @if ($noticia->es_oficial && $noticia->fuente_url)
                                <a href="{{ $noticia->fuente_url }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-zinc-400 hover:text-cyan-400">Fuente original</a>
                            @else
                                <a href="{{ route('noticias.show', [$team->slug, $noticia]) }}" wire:navigate class="font-semibold text-zinc-400 hover:text-cyan-400">Leer más &rarr;</a>
                            @endif
                        </div>
                        <div class="news-card-comments">
                            <div class="news-card-comments-heading">
                                <h3>Comentarios</h3>
                                <span>{{ $noticia->comentarios->count() }}</span>
                            </div>
                            <div class="news-card-comments-list">
                                @forelse ($noticia->comentarios as $comentario)
                                    <p class="news-card-comment">
                                        <span class="news-card-comment-header">
                                            <strong>{{ $comentario->autor?->name ?? 'Usuario' }}</strong>
                                            @if ($comentario->user_id === auth()->id())
                                                <span class="news-card-comment-actions">
                                                    <button type="submit" form="delete-comment-{{ $comentario->id }}" class="news-card-delete-trigger">Eliminar</button>
                                                </span>
                                            @endif
                                        </span>
                                        <span class="news-card-comment-content" data-comment-content="{{ $comentario->id }}">{{ $comentario->contenido }}</span>
                                        @if ($comentario->user_id === auth()->id())
                                            <form method="POST" action="{{ route('noticias.comentarios.update', [$team->slug, $noticia, $comentario]) }}" class="news-card-edit-form" data-comment-edit-form="{{ $comentario->id }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="contenido" value="{{ $comentario->contenido }}" maxlength="2000" aria-label="Editar comentario" required>
                                                <button type="submit">Guardar</button>
                                            </form>
                                            <form id="delete-comment-{{ $comentario->id }}" method="POST" action="{{ route('noticias.comentarios.destroy', [$team->slug, $noticia, $comentario]) }}" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </p>
                                @empty
                                    <p class="news-card-comments-empty">Todavía no hay respuestas.</p>
                                @endforelse
                            </div>
                            <form method="POST" action="{{ route('noticias.comentarios.store', [$team->slug, $noticia]) }}" class="news-card-comment-form">
                                @csrf
                                <input type="text" name="contenido" maxlength="2000" placeholder="Escribe una respuesta..." aria-label="Escribe una respuesta" required>
                                <button type="submit">Responder</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <p class="rounded-2xl border border-dashed border-zinc-700 p-8 text-sm text-zinc-400 sm:col-span-2 lg:col-span-3">Todavía no hay noticias disponibles.</p>
            @endforelse
        </div>

        {{-- Paginación para navegar entre páginas de noticias. --}}
        @if ($items->hasPages())
            <div class="pt-4">{{ $items->links() }}</div>
        @endif
    </div>
</x-layouts::app>
