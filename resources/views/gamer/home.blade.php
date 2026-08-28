<x-layouts::app :title="$team->name.' | Comunidad gamer'">
    <div class="mx-auto max-w-7xl space-y-8 p-6 lg:p-10">
        <div class="rounded-2xl bg-zinc-950 p-8 text-white shadow-xl dark:bg-black">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-400">{{ $team->name }}</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight">Tu centro de juego</h1>
            <p class="mt-3 max-w-2xl text-zinc-300">Noticias, comunidades y soporte reunidos en el espacio de tu equipo.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900"><p class="text-sm text-zinc-500">Problemas abiertos</p><p class="mt-2 text-3xl font-bold">{{ $problemas_abiertos }}</p></div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900"><p class="text-sm text-zinc-500">Comentarios</p><p class="mt-2 text-3xl font-bold">{{ $comentarios_totales }}</p></div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900"><p class="text-sm text-zinc-500">Comunidades activas</p><p class="mt-2 text-3xl font-bold">{{ $comunidades_activas->count() }}</p></div>
        </div>

        <div class="grid gap-8 lg:grid-cols-2">
            <section class="space-y-4">
                <div class="flex items-center justify-between"><h2 class="text-xl font-semibold">Noticias recientes</h2><a class="text-sm font-semibold text-amber-600" href="{{ route('noticias.index', $team->slug) }}">Ver todas</a></div>
                @forelse ($noticias_recientes as $noticia)
                    <a href="{{ route('noticias.show', [$team->slug, $noticia]) }}" class="block rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900"><p class="font-semibold">{{ $noticia->titulo }}</p><p class="mt-2 line-clamp-2 text-sm text-zinc-500">{{ $noticia->contenido }}</p></a>
                @empty
                    <p class="rounded-xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-500">Todavía no hay noticias.</p>
                @endforelse
            </section>
            <section class="space-y-4">
                <div class="flex items-center justify-between"><h2 class="text-xl font-semibold">Comunidades activas</h2><a class="text-sm font-semibold text-amber-600" href="{{ route('comunidad.index', $team->slug) }}">Explorar</a></div>
                @forelse ($comunidades_activas as $comunidad)
                    <a href="{{ route('comunidad.show', [$team->slug, $comunidad]) }}" class="block rounded-xl border border-zinc-200 bg-white p-5 transition hover:border-amber-400 dark:border-zinc-700 dark:bg-zinc-900"><p class="font-semibold">{{ $comunidad->nombre }}</p><p class="mt-2 line-clamp-2 text-sm text-zinc-500">{{ $comunidad->descripcion ?: 'Una nueva comunidad gamer.' }}</p></a>
                @empty
                    <p class="rounded-xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-500">Todavía no hay comunidades.</p>
                @endforelse
            </section>
        </div>
    </div>
</x-layouts::app>