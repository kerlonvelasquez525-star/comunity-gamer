<x-layouts::app :title="$title">
    <div class="mx-auto max-w-7xl space-y-6 p-6 lg:p-10">
        <div class="flex flex-wrap items-end justify-between gap-4"><div><p class="text-sm font-semibold uppercase tracking-[0.18em] text-amber-600">{{ $team->name }}</p><h1 class="mt-2 text-3xl font-bold">{{ $title }}</h1></div><a href="{{ route($resource.'.create', $team->slug) }}" class="rounded-lg bg-amber-500 px-4 py-2 font-semibold text-zinc-950 hover:bg-amber-400">Crear nuevo</a></div>
        @if (session('status'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('status') }}</div>@endif
        <div class="grid gap-4 md:grid-cols-2">
            @forelse ($items as $item)
                @php($itemTitle = $resource === 'comunidad' ? $item->nombre : ($item->titulo ?? 'Comentario'))
                @php($itemText = $resource === 'comentarios' ? $item->contenido : ($item->descripcion ?? $item->contenido ?? ''))
                <article class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900"><div class="flex items-start justify-between gap-4"><div><h2 class="font-semibold">{{ $itemTitle }}</h2><p class="mt-2 line-clamp-3 text-sm text-zinc-500">{{ $itemText }}</p></div>@if ($resource === 'problemas')<span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">{{ $item->estado }}</span>@endif</div><div class="mt-5 flex items-center justify-between"><a class="text-sm font-semibold text-amber-600" href="{{ route($resource.'.show', [$team->slug, $item]) }}">Abrir detalle</a><a class="text-sm text-zinc-500 hover:text-zinc-900 dark:hover:text-white" href="{{ route($resource.'.edit', [$team->slug, $item]) }}">Editar</a></div></article>
            @empty
                <p class="rounded-xl border border-dashed border-zinc-300 p-8 text-sm text-zinc-500">No hay contenido en este equipo todavía.</p>
            @endforelse
        </div>
        <div>{{ $items->links() }}</div>
    </div>
</x-layouts::app>