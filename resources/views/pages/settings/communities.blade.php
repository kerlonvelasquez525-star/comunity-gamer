<x-layouts::app :title="__('Create community')">
    <div class="mx-auto w-full max-w-3xl space-y-6 p-6 lg:p-10">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-amber-600">{{ $team->name }} // configuración</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">Crear comunidad</h1>
            <p class="mt-2 max-w-2xl text-sm text-zinc-500 dark:text-zinc-400">Define el perfil de jugadores para que tu comunidad pueda encontrarse fácilmente.</p>
        </div>

        <form method="POST" action="{{ route('comunidad.store', $team->slug) }}" class="space-y-5 rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900" enctype="multipart/form-data">
            @csrf
            <label class="block text-sm font-semibold text-zinc-800 dark:text-zinc-100">Nombre
                <input name="nombre" value="{{ old('nombre') }}" required maxlength="100" class="mt-2 block w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800">
            </label>
            <label class="block text-sm font-semibold text-zinc-800 dark:text-zinc-100">Descripción
                <textarea name="descripcion" rows="4" maxlength="1000" class="mt-2 block w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800">{{ old('descripcion') }}</textarea>
            </label>
            <label class="block text-sm font-semibold text-zinc-800 dark:text-zinc-100">Imagen de la comunidad
                <input name="imagen" type="file" accept="image/*" class="mt-2 block w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800">
            </label>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ([
                    'juego_principal' => ['Juego principal', 'Ej. Valorant, Fortnite, Minecraft'],
                    'plataforma' => ['Plataforma', 'PC, PlayStation, Xbox, Multiplataforma'],
                    'region' => ['Región', 'LATAM, Europa, Global'],
                    'idioma' => ['Idioma', 'Español, Inglés, Portugués'],
                    'modalidad' => ['Modalidad', 'Casual, Competitiva, Ranked, Cooperativa'],
                    'horario' => ['Horario habitual', 'Noche, Fines de semana, Flexible'],
                ] as $field => [$label, $placeholder])
                    <label class="block text-sm font-semibold text-zinc-800 dark:text-zinc-100">{{ $label }}
                        <input name="{{ $field }}" value="{{ old($field) }}" maxlength="100" placeholder="{{ $placeholder }}" class="mt-2 block w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800">
                    </label>
                @endforeach
                <label class="block text-sm font-semibold text-zinc-800 dark:text-zinc-100">Tipo
                    <select name="tipo" class="mt-2 block w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800"><option value="publica">Pública</option><option value="privada">Privada</option><option value="cerrada">Cerrada</option></select>
                </label>
                <label class="block text-sm font-semibold text-zinc-800 dark:text-zinc-100">Nivel
                    <select name="nivel" class="mt-2 block w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800"><option value="Casual">Casual</option><option value="Competitivo">Competitivo</option><option value="Pro gamer">Pro gamer</option></select>
                </label>
                <label class="block text-sm font-semibold text-zinc-800 dark:text-zinc-100">Rango
                    <input name="rango" value="{{ old('rango') }}" maxlength="50" placeholder="Ej. Oro, Diamante, Sin rango" class="mt-2 block w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800">
                </label>
                <label class="block text-sm font-semibold text-zinc-800 dark:text-zinc-100">Máximo de miembros
                    <input name="max_miembros" type="number" min="2" max="500" value="{{ old('max_miembros') }}" class="mt-2 block w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800">
                </label>
            </div>
            @if ($errors->any())
                <div role="alert" class="text-sm text-red-600"><ul class="list-disc ps-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            <button type="submit" class="rounded-lg bg-amber-500 px-5 py-2 font-semibold text-zinc-950 hover:bg-amber-400">Crear comunidad</button>
        </form>
    </div>
</x-layouts::app>
