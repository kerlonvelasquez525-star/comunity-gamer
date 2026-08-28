<x-layouts::app :title="__('Noticias')">
    <div class="mx-auto w-full max-w-7xl space-y-8 p-6 lg:p-10">

        <header class="flex flex-wrap items-end justify-between gap-4">
            <div class="space-y-3">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-amber-600">
                    {{ $team->name }} // transmisión en vivo
                </p>
                <h1 class="text-4xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    {{ $title }}
                </h1>
                <p class="max-w-2xl text-zinc-500 dark:text-zinc-400">
                    Todo lo que pasa en tus juegos, filtrado y clasificado para tu comunidad.
                </p>
            </div>

            <a href="{{ route('noticias.create', $team->slug) }}" wire:navigate
               class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-zinc-950 transition hover:bg-amber-400">
                Publicar noticia
            </a>
        </header>

        @if (session('status'))
            <div role="status"
                 class="rounded-lg border border-emerald-300 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <form method="GET" action="{{ route('noticias.index', $team->slug) }}"
              class="flex flex-wrap items-center gap-3 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <label for="buscar" class="sr-only">Buscar noticias</label>
            <input id="buscar" name="buscar" type="search" value="{{ request('buscar') }}"
                   placeholder="Buscar por título…" maxlength="180"
                   class="min-w-56 flex-1 rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">

            <label for="categoria" class="sr-only">Categoría</label>
            <input id="categoria" name="categoria" type="text" value="{{ request('categoria') }}"
                   placeholder="Categoría" maxlength="80"
                   class="w-44 rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">

            <button type="submit"
                    class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
                Filtrar
            </button>

            @if (request()->hasAny(['buscar', 'categoria']))
                <a href="{{ route('noticias.index', $team->slug) }}" wire:navigate
                   class="text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white">
                    Limpiar
                </a>
            @endif
        </form>

        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            <span class="font-semibold text-amber-600">{{ $items->total() }}</span>
            {{ $items->total() === 1 ? 'noticia encontrada' : 'noticias encontradas' }}
        </p>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($items as $noticia)
                <article class="flex flex-col overflow-hidden rounded-xl border border-zinc-200 bg-white transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900">
                    @if (filled($noticia->imagen_url))
                        <img src="{{ $noticia->imagen_url }}" alt="" loading="lazy" decoding="async"
                             class="h-44 w-full object-cover">
                    @endif

                    <div class="flex flex-1 flex-col gap-3 p-5">
                        <div class="flex items-center justify-between gap-3">
                            @if (filled($noticia->categoria))
                                <span class="rounded-md bg-amber-100 px-2 py-1 text-xs font-bold uppercase tracking-wide text-amber-800 dark:bg-amber-500/15 dark:text-amber-300">
                                    {{ $noticia->categoria }}
                                </span>
                            @else
                                <span></span>
                            @endif
                            <time datetime="{{ $noticia->created_at?->toDateString() }}"
                                  class="text-xs text-zinc-400">
                                {{ $noticia->created_at?->diffForHumans() }}
                            </time>
                        </div>

                        <h2 class="text-lg font-bold leading-snug text-zinc-900 dark:text-white">
                            <a href="{{ route('noticias.show', [$team->slug, $noticia]) }}" wire:navigate
                               class="hover:text-amber-600">
                                {{ $noticia->titulo }}
                            </a>
                        </h2>

                        <p class="line-clamp-3 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $noticia->contenido }}
                        </p>

                        <div class="mt-auto flex items-center justify-between border-t border-zinc-100 pt-3 dark:border-zinc-800">
                            <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-300">
                                {{ $noticia->autor?->name ?? 'Autor desconocido' }}
                            </span>
                            <a href="{{ route('noticias.edit', [$team->slug, $noticia]) }}" wire:navigate
                               class="text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white">
                                Editar
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <p class="rounded-xl border border-dashed border-zinc-300 p-8 text-sm text-zinc-500 md:col-span-2 xl:col-span-3 dark:border-zinc-700">
                    Todavía no hay noticias en este equipo.
                    <a href="{{ route('noticias.create', $team->slug) }}" wire:navigate
                       class="font-semibold text-amber-600 hover:underline">Publica la primera</a>.
                </p>
            @endforelse
        </div>

        @if ($items->hasPages())
            <div>{{ $items->links() }}</div>
        @endif
    </div>
</x-layouts::app>