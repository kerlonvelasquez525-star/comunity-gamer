<?php

namespace App\Http\Controllers;

use App\Models\Foro;
use App\Models\Hilo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class HiloController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [Hilo::class, $team]);

        $items = Hilo::query()
            ->whereHas('foro.comunidad', fn ($query) => $query->where('team_id', $team->id))
            ->with(['foro.comunidad', 'autor'])
            ->when($request->filled('buscar'), fn ($query) => $query->where('titulo', 'like', '%'.$request->string('buscar').'%'))
            ->latest('fecha_creacion')
            ->paginate($request->integer('per_page', 12))
            ->appends($request->query());

        return $request->expectsJson()
            ? response()->json($items)
            : response()->view('gamer.index', [
                'resource' => 'hilos',
                'title' => 'Hilos',
                'items' => $items,
                'team' => $team,
            ]);
    }

    public function create(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Hilo::class, $team]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Formulario de creación disponible.'])
            : response()->view('gamer.form', [
                'resource' => 'hilos',
                'title' => 'Nuevo hilo',
                'team' => $team,
            ]);
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Hilo::class, $team]);

        $validated = $request->validate([
            'id_foro' => ['required', 'integer', 'exists:foros,id_foro'],
            'titulo' => ['required', 'string', 'max:150'],
            'contenido' => ['required', 'string', 'max:20000'],
        ]);

        $foro = Foro::query()->whereKey($validated['id_foro'])->firstOrFail();

        if ($foro->comunidad?->team_id !== $team->id) {
            abort(403);
        }

        $item = Hilo::create([
            ...$validated,
            'id_usuario' => $request->user()->id,
            'fecha_creacion' => now(),
        ]);

        return $request->expectsJson()
            ? response()->json($item->load(['foro.comunidad', 'autor']), 201)
            : redirect()->route('hilos.show', [$team->slug, $item])->with('status', 'Hilo creado.');
    }

    public function show(Request $request, string $current_team, Hilo $hilo): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$hilo, $team]);

        return $request->expectsJson()
            ? response()->json($hilo->load(['foro.comunidad', 'autor']))
            : response()->view('gamer.show', [
                'resource' => 'hilos',
                'title' => $hilo->titulo,
                'item' => $hilo->load(['foro.comunidad', 'autor']),
                'team' => $team,
            ]);
    }

    public function edit(Request $request, string $current_team, Hilo $hilo): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$hilo, $team]);

        return $request->expectsJson()
            ? response()->json($hilo)
            : response()->view('gamer.form', [
                'resource' => 'hilos',
                'title' => 'Editar hilo',
                'item' => $hilo,
                'team' => $team,
            ]);
    }

    public function update(Request $request, string $current_team, Hilo $hilo): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$hilo, $team]);

        $validated = $request->validate([
            'id_foro' => ['sometimes', 'required', 'integer', 'exists:foros,id_foro'],
            'titulo' => ['sometimes', 'required', 'string', 'max:150'],
            'contenido' => ['sometimes', 'required', 'string', 'max:20000'],
        ]);

        if (isset($validated['id_foro'])) {
            $foro = Foro::query()->whereKey($validated['id_foro'])->firstOrFail();
            if ($foro->comunidad?->team_id !== $team->id) {
                abort(403);
            }
        }

        $hilo->update($validated);

        return $request->expectsJson()
            ? response()->json($hilo->fresh())
            : redirect()->route('hilos.show', [$team->slug, $hilo])->with('status', 'Hilo actualizado.');
    }

    public function destroy(Request $request, string $current_team, Hilo $hilo): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('delete', [$hilo, $team]);

        $hilo->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Hilo eliminado correctamente.'])
            : redirect()->route('hilos.index', $team->slug)->with('status', 'Hilo eliminado.');
    }
}
