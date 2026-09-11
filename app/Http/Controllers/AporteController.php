<?php

namespace App\Http\Controllers;

use App\Models\Aporte;
use App\Models\Hilo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AporteController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [Aporte::class, $team]);

        $items = Aporte::query()
            ->whereHas('hilo.foro.comunidad', fn ($query) => $query->where('team_id', $team->id))
            ->with(['hilo.foro.comunidad', 'autor'])
            ->latest('fecha_aporte')
            ->paginate($request->integer('per_page', 12))
            ->appends($request->query());

        return $request->expectsJson()
            ? response()->json($items)
            : response()->view('gamer.index', [
                'resource' => 'aportes',
                'title' => 'Aportes',
                'items' => $items,
                'team' => $team,
            ]);
    }

    public function create(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Aporte::class, $team]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Formulario de creación disponible.'])
            : response()->view('gamer.form', [
                'resource' => 'aportes',
                'title' => 'Nuevo aporte',
                'team' => $team,
            ]);
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Aporte::class, $team]);

        $validated = $request->validate([
            'id_hilo' => ['required', 'integer', 'exists:hilos,id_hilo'],
            'contenido' => ['required', 'string', 'max:12000'],
        ]);

        $hilo = Hilo::query()->whereKey($validated['id_hilo'])->firstOrFail();

        if ($hilo->foro?->comunidad?->team_id !== $team->id) {
            abort(403);
        }

        $item = Aporte::create([
            ...$validated,
            'id_usuario' => $request->user()->id,
            'fecha_aporte' => now(),
        ]);

        return $request->expectsJson()
            ? response()->json($item->load(['hilo.foro.comunidad', 'autor']), 201)
            : redirect()->route('aportes.show', [$team->slug, $item])->with('status', 'Aporte registrado.');
    }

    public function show(Request $request, string $current_team, Aporte $aporte): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$aporte, $team]);

        return $request->expectsJson()
            ? response()->json($aporte->load(['hilo.foro.comunidad', 'autor']))
            : response()->view('gamer.show', [
                'resource' => 'aportes',
                'title' => 'Aporte',
                'item' => $aporte->load(['hilo.foro.comunidad', 'autor']),
                'team' => $team,
            ]);
    }

    public function edit(Request $request, string $current_team, Aporte $aporte): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$aporte, $team]);

        return $request->expectsJson()
            ? response()->json($aporte)
            : response()->view('gamer.form', [
                'resource' => 'aportes',
                'title' => 'Editar aporte',
                'item' => $aporte,
                'team' => $team,
            ]);
    }

    public function update(Request $request, string $current_team, Aporte $aporte): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$aporte, $team]);

        $validated = $request->validate([
            'contenido' => ['sometimes', 'required', 'string', 'max:12000'],
        ]);

        $aporte->update($validated);

        return $request->expectsJson()
            ? response()->json($aporte->fresh())
            : redirect()->route('aportes.show', [$team->slug, $aporte])->with('status', 'Aporte actualizado.');
    }

    public function destroy(Request $request, string $current_team, Aporte $aporte): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('delete', [$aporte, $team]);

        $aporte->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Aporte eliminado correctamente.'])
            : redirect()->route('aportes.index', $team->slug)->with('status', 'Aporte eliminado.');
    }
}
