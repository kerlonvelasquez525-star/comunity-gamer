<x-layouts::app :title="__('Comunidades')">
    <div class="mx-auto w-full max-w-7xl space-y-8 p-6 lg:p-10">

        <header class="flex flex-wrap items-end justify-between gap-4">
            <div class="space-y-3">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-amber-600">
                    {{ $team->name }} // red gamer
                </p>
                <h1 class="text-4xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    {{ $title }}
                </h1>
                <p class="max-w-2xl text-zinc-500 dark:text-zinc-400">
                    Encuentra grupos, comparte intereses y juega acompañado.
                </p>
            </div>

            <a href="{{ route('comunidad.create', $team->slug) }}" wire:navigate
               class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-zinc-950 transition hover:bg-amber-400">
                Crear comunidad
            </a>
        </header>

        @if (session('status'))
            <div role="status"
                 class="rounded-lg border border-emerald-300 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <form method="GET" action="{{ route('comunidad.index', $team->slug) }}"
              class="flex flex-wrap items-center gap-3 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <label for="buscar" class="sr-only">Buscar comunidades</label>
            <input id="buscar" name="buscar" type="search" value="{{ request('buscar') }}"
                   placeholder="Buscar comunidad por nombre…" maxlength="100"
                   class="min-w-56 flex-1 rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">

            <button type="submit"
                    class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
                Buscar
            </button>

            @if (request()->filled('buscar'))
                <a href="{{ route('comunidad.index', $team->slug) }}" wire:navigate
                   class="text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white">
                    Limpiar
                </a>
            @endif
        </form>

        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            <span class="font-semibold text-amber-600">{{ $items->total() }}</span>
            {{ $items->total() === 1 ? 'comunidad disponible' : 'comunidades disponibles' }}
        </p>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($items as $comunidad)
                <article class="flex flex-col gap-4 rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-start gap-3">
                        <span aria-hidden="true"
                              class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-amber-500 text-lg font-bold text-zinc-950">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($comunidad->nombre, 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <h2 class="truncate text-lg font-bold text-zinc-900 dark:text-white">
                                <a href="{{ route('comunidad.show', [$team->slug, $comunidad]) }}" wire:navigate
                                   class="hover:text-amber-600">
                                    {{ $comunidad->nombre }}
                                </a>
                            </h2>
                            <p class="text-xs text-zinc-400">
                                Creada por {{ $comunidad->creador?->name ?? 'un miembro' }}
                            </p>
                        </div>
                    </div>

                    <p class="line-clamp-3 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ $comunidad->descripcion ?: 'Una nueva comunidad gamer, todavía sin descripción.' }}
                    </p>

                    <div class="mt-auto flex items-center justify-between border-t border-zinc-100 pt-3 dark:border-zinc-800">
                        <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-300">
                            {{ $comunidad->miembros_count }}
                            {{ $comunidad->miembros_count === 1 ? 'miembro' : 'miembros' }}
                        </span>
                        <a href="{{ route('comunidad.edit', [$team->slug, $comunidad]) }}" wire:navigate
                           class="text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white">
                            Editar
                        </a>
                    </div>
                </article>
            @empty
                <p class="rounded-xl border border-dashed border-zinc-300 p-8 text-sm text-zinc-500 md:col-span-2 xl:col-span-3 dark:border-zinc-700">
                    Este equipo aún no tiene comunidades.
                    <a href="{{ route('comunidad.create', $team->slug) }}" wire:navigate
                       class="font-semibold text-amber-600 hover:underline">Crea la primera</a>.
                </p>
            @endforelse
        </div>

        @if ($items->hasPages())
            <div>{{ $items->links() }}</div>
        @endif
    </div>
</x-layouts::app>