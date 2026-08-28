<x-layouts::app :title="__('Comentarios')">
    <div class="mx-auto w-full max-w-5xl space-y-8 p-6 lg:p-10">

        <header class="flex flex-wrap items-end justify-between gap-4">
            <div class="space-y-3">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-amber-600">
                    {{ $team->name }} // canal abierto
                </p>
                <h1 class="text-4xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    {{ $title }}
                </h1>
                <p class="max-w-2xl text-zinc-500 dark:text-zinc-400">
                    Comparte ideas, descubre estrategias y conversa con jugadores de tu equipo.
                </p>
            </div>
          <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
            <a href="{{ route('comentarios.create', $team->slug) }}" wire:navigate
               class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-zinc-950 transition hover:bg-amber-400">
                Escribir comentario 
            </a>
        </header>

        @if (session('status'))
            <div role="status"
                 class="rounded-lg border border-emerald-300 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <form method="GET" action="{{ route('comentarios.index', $team->slug) }}"
              class="flex flex-wrap items-center gap-3 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <label for="publicacion_id" class="text-sm font-medium text-zinc-600 dark:text-zinc-300">
                Filtrar por publicación
            </label>
            <input id="publicacion_id" name="publicacion_id" type="number" min="1"
                   value="{{ request('publicacion_id') }}" placeholder="ID"
                   class="w-32 rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">

            <button type="submit"
                    class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
                Aplicar
            </button>

            @if (request()->filled('publicacion_id'))
                <a href="{{ route('comentarios.index', $team->slug) }}" wire:navigate
                   class="text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white">
                    Ver todos
                </a>
            @endif
        </form>

        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            <span class="font-semibold text-amber-600">{{ $items->total() }}</span>
            {{ $items->total() === 1 ? 'comentario' : 'comentarios' }} en este equipo
        </p>

        <ul class="space-y-4">
            @forelse ($items as $comentario)
                @php
                    $fecha = filled($comentario->fecha_comentario)
                        ? \Illuminate\Support\Carbon::parse($comentario->fecha_comentario)
                        : null;
                @endphp

                <li class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span aria-hidden="true"
                                  class="flex size-9 items-center justify-center rounded-full bg-amber-500 text-sm font-bold text-zinc-950">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($comentario->autor?->name ?? '?', 0, 1)) }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                                    {{ $comentario->autor?->name ?? 'Usuario eliminado' }}
                                </p>
                                <p class="text-xs text-zinc-400">
                                    Publicación #{{ $comentario->id_publicacion }}
                                    @if ($fecha)
                                        · <time datetime="{{ $fecha->toIso8601String() }}">{{ $fecha->diffForHumans() }}</time>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex shrink-0 gap-3 text-xs">
                            <a href="{{ route('comentarios.show', [$team->slug, $comentario]) }}" wire:navigate
                               class="font-semibold text-amber-600 hover:underline">Ver</a>
                            <a href="{{ route('comentarios.edit', [$team->slug, $comentario]) }}" wire:navigate
                               class="font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white">Editar</a>
                        </div>
                    </div>

                    <p class="mt-4 whitespace-pre-line text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">
                        {{ $comentario->contenido }}
                    </p>
                </li>
            @empty
                <li class="rounded-xl border border-dashed border-zinc-300 p-8 text-sm text-zinc-500 dark:border-zinc-700">
                    Nadie ha comentado todavía.
                    <a href="{{ route('comentarios.create', $team->slug) }}" wire:navigate
                       class="font-semibold text-amber-600 hover:underline">Abre la conversación</a>.
                </li>
            @endforelse
        </ul>

        @if ($items->hasPages())
            <div>{{ $items->links() }}</div>
        @endif
    </div>
</x-layouts::app>