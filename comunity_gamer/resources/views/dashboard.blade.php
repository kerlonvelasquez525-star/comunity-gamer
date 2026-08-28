<x-layouts::app :title="__('Dashboard')">
    <livewire:pages::teams.pending-invitations-modal />

    <div class="mx-auto w-full max-w-7xl space-y-8 p-6 lg:p-10">

        <div class="rounded-2xl bg-zinc-950 p-8 text-white shadow-xl dark:bg-black">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-400">{{ $team->name }}</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight">Tu centro de juego</h1>
            <p class="mt-3 max-w-2xl text-zinc-300">
                Noticias, comunidades y soporte reunidos en el espacio de tu equipo.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Noticias publicadas', 'value' => $noticias_totales,     'route' => 'noticias.index'],
                ['label' => 'Comentarios',         'value' => $comentarios_totales,  'route' => 'comentarios.index'],
                ['label' => 'Comunidades',         'value' => $comunidades_activas->count(), 'route' => 'comunidad.index'],
                ['label' => 'Problemas abiertos',  'value' => $problemas_abiertos,   'route' => 'problemas.index'],
            ] as $card)
                <a href="{{ route($card['route'], $team->slug) }}" wire:navigate
                   class="rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $card['label'] }}</p>
                    <p class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">{{ $card['value'] }}</p>
                </a>
            @endforeach
        </div>

        <div class="grid gap-8 lg:grid-cols-2">
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">Noticias recientes</h2>
                    <a href="{{ route('noticias.index', $team->slug) }}" wire:navigate
                       class="text-sm font-semibold text-amber-600 hover:underline">Ver todas</a>
                </div>

                @forelse ($noticias_recientes as $noticia)
                    <a href="{{ route('noticias.show', [$team->slug, $noticia]) }}" wire:navigate
                       class="block rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900">
                        <p class="font-semibold text-zinc-900 dark:text-white">{{ $noticia->titulo }}</p>
                        <p class="mt-2 line-clamp-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $noticia->contenido }}</p>
                        <p class="mt-3 text-xs text-zinc-400">
                            {{ $noticia->autor?->name ?? 'Autor desconocido' }} · {{ $noticia->created_at?->diffForHumans() }}
                        </p>
                    </a>
                @empty
                    <p class="rounded-xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-500 dark:border-zinc-700">
                        Todavía no hay noticias.
                    </p>
                @endforelse
            </section>

            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">Comunidades activas</h2>
                    <a href="{{ route('comunidad.index', $team->slug) }}" wire:navigate
                       class="text-sm font-semibold text-amber-600 hover:underline">Explorar</a>
                </div>

                @forelse ($comunidades_activas as $comunidad)
                    <a href="{{ route('comunidad.show', [$team->slug, $comunidad]) }}" wire:navigate
                       class="block rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900">
                        <p class="font-semibold text-zinc-900 dark:text-white">{{ $comunidad->nombre }}</p>
                        <p class="mt-2 line-clamp-2 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $comunidad->descripcion ?: 'Una nueva comunidad gamer.' }}
                        </p>
                        <p class="mt-3 text-xs text-zinc-400">
                            {{ $comunidad->miembros_count }}
                            {{ $comunidad->miembros_count === 1 ? 'miembro' : 'miembros' }}
                        </p>
                    </a>
                @empty
                    <p class="rounded-xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-500 dark:border-zinc-700">
                        Todavía no hay comunidades.
                    </p>
                @endforelse
            </section>
        </div>
    </div>
</x-layouts::app>