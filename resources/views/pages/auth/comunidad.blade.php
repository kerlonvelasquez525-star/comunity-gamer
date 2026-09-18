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
                <article class="internal-community-card">
                    <div class="internal-community-image-wrap">
                        <img src="{{ asset('nexus.png') }}" alt="{{ $comunidad->nombre }}" class="internal-community-image" loading="lazy">
                        <span class="internal-community-badge">COMUNIDAD ACTIVA</span>
                    </div>
                    <div class="internal-community-content">
                        <p class="internal-community-owner">Creada por {{ $comunidad->creador?->name ?? 'un miembro' }} · Comunidad pública</p>
                        <h2>{{ $comunidad->nombre }}</h2>
                        <p class="internal-community-description">{{ $comunidad->descripcion ?: 'Una nueva comunidad gamer, todavía sin descripción.' }}</p>
                        <div class="internal-community-tags">
                            @foreach ([$comunidad->juego_principal, $comunidad->plataforma, $comunidad->region, $comunidad->modalidad, $comunidad->horario] as $tag)
                                @if (filled($tag))
                                    <span>{{ $tag }}</span>
                                @endif
                            @endforeach
                        </div>
                        <div class="internal-community-meta">
                            <span>{{ $comunidad->miembros_count }} {{ $comunidad->miembros_count === 1 ? 'miembro' : 'miembros' }}</span>
                            <span>Activa</span>
                        </div>
                        @can('update', [$comunidad, $team])
                            <a href="{{ route('comunidad.edit', [$team->slug, $comunidad]) }}" wire:navigate class="internal-community-action">Editar</a>
                        @else
                            <a href="{{ route('public-comunidad.index') }}" wire:navigate class="internal-community-action">Ver y solicitar ingreso &rarr;</a>
                        @endcan
                    </div>
                </article>
            @empty
                <p class="rounded-xl border border-dashed border-zinc-300 p-8 text-sm text-zinc-500 md:col-span-2 xl:col-span-3 dark:border-zinc-700">
                        Este equipo aún no tiene comunidades propias. Revisa las comunidades públicas disponibles o crea una desde Configuración.
                </p>
            @endforelse
        </div>

        @if ($items->hasPages())
            <div>{{ $items->links() }}</div>
        @endif
    </div>
</x-layouts::app>