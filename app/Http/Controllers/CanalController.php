<?php

namespace App\Http\Controllers;

use App\Models\Canal;
use App\Models\Comunidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CanalController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [Canal::class, $team]);

        $items = Canal::query()
            ->whereHas('comunidad', fn ($query) => $query->where('team_id', $team->id))
            ->with('comunidad')
            ->latest('fecha_creacion')
            ->paginate($request->integer('per_page', 12))
            ->appends($request->query());

        return $request->expectsJson()
            ? response()->json($items)
            : response()->view('gamer.index', [
                'resource' => 'canales',
                'title' => 'Canales',
                'items' => $items,
                'team' => $team,
            ]);
    }

    public function create(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Canal::class, $team]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Formulario de creación disponible.'])
            : response()->view('gamer.form', [
                'resource' => 'canales',
                'title' => 'Nuevo canal',
                'team' => $team,
            ]);
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Canal::class, $team]);

        $validated = $request->validate([
            'comunidad_id' => ['required', 'integer', 'exists:comunidades,id'],
            'nombre' => ['required', 'string', 'max:100'],
            'tipo' => ['required', 'in:texto,voz'],
        ]);

        Comunidad::query()
            ->whereKey($validated['comunidad_id'])
            ->where('team_id', $team->id)
            ->firstOrFail();

        $item = Canal::create([
            ...$validated,
            'fecha_creacion' => now(),
        ]);

        return $request->expectsJson()
            ? response()->json($item->load('comunidad'), 201)
            : redirect()->route('canales.show', [$team->slug, $item])->with('status', 'Canal creado.');
    }

    public function show(Request $request, string $current_team, Canal $canal): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$canal, $team]);

        return $request->expectsJson()
            ? response()->json($canal->load('comunidad'))
            : response()->view('gamer.show', [
                'resource' => 'canales',
                'title' => $canal->nombre,
                'item' => $canal->load('comunidad'),
                'team' => $team,
            ]);
    }

    public function edit(Request $request, string $current_team, Canal $canal): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$canal, $team]);

        return $request->expectsJson()
            ? response()->json($canal)
            : response()->view('gamer.form', [
                'resource' => 'canales',
                'title' => 'Editar canal',
                'item' => $canal,
                'team' => $team,
            ]);
    }

    public function update(Request $request, string $current_team, Canal $canal): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$canal, $team]);

        $validated = $request->validate([
            'comunidad_id' => ['sometimes', 'required', 'integer', 'exists:comunidades,id'],
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'tipo' => ['sometimes', 'required', 'in:texto,voz'],
        ]);

        if (isset($validated['comunidad_id'])) {
            Comunidad::query()
                ->whereKey($validated['comunidad_id'])
                ->where('team_id', $team->id)
                ->firstOrFail();
        }

        $canal->update($validated);

        return $request->expectsJson()
            ? response()->json($canal->fresh())
            : redirect()->route('canales.show', [$team->slug, $canal])->with('status', 'Canal actualizado.');
    }

    public function destroy(Request $request, string $current_team, Canal $canal): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('delete', [$canal, $team]);

        $canal->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Canal eliminado correctamente.'])
            : redirect()->route('canales.index', $team->slug)->with('status', 'Canal eliminado.');
    }
}
