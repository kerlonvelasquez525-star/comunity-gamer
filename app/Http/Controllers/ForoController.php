<?php

namespace App\Http\Controllers;

use App\Models\Comunidad;
use App\Models\Foro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ForoController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [Foro::class, $team]);

        $items = Foro::query()
            ->whereHas('comunidad', fn ($query) => $query->where('team_id', $team->id))
            ->with('comunidad')
            ->when($request->filled('buscar'), fn ($query) => $query->where('nombre', 'like', '%'.$request->string('buscar').'%'))
            ->latest('fecha_creacion')
            ->paginate($request->integer('per_page', 12))
            ->appends($request->query());

        return $request->expectsJson()
            ? response()->json($items)
            : response()->view('gamer.index', [
                'resource' => 'foros',
                'title' => 'Foros',
                'items' => $items,
                'team' => $team,
            ]);
    }

    public function create(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Foro::class, $team]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Formulario de creación disponible.'])
            : response()->view('gamer.form', [
                'resource' => 'foros',
                'title' => 'Nuevo foro',
                'team' => $team,
            ]);
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Foro::class, $team]);

        $validated = $request->validate([
            'comunidad_id' => ['required', 'integer', 'exists:comunidades,id'],
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
        ]);

        $comunidad = Comunidad::query()
            ->whereKey($validated['comunidad_id'])
            ->where('team_id', $team->id)
            ->firstOrFail();

        $item = Foro::create([
            ...$validated,
            'comunidad_id' => $comunidad->id,
            'fecha_creacion' => now(),
        ]);

        return $request->expectsJson()
            ? response()->json($item->load('comunidad'), 201)
            : redirect()->route('foros.show', [$team->slug, $item])->with('status', 'Foro creado.');
    }

    public function show(Request $request, string $current_team, Foro $foro): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$foro, $team]);

        return $request->expectsJson()
            ? response()->json($foro->load('comunidad'))
            : response()->view('gamer.show', [
                'resource' => 'foros',
                'title' => $foro->nombre,
                'item' => $foro->load('comunidad'),
                'team' => $team,
            ]);
    }

    public function edit(Request $request, string $current_team, Foro $foro): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$foro, $team]);

        return $request->expectsJson()
            ? response()->json($foro)
            : response()->view('gamer.form', [
                'resource' => 'foros',
                'title' => 'Editar foro',
                'item' => $foro,
                'team' => $team,
            ]);
    }

    public function update(Request $request, string $current_team, Foro $foro): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$foro, $team]);

        $validated = $request->validate([
            'comunidad_id' => ['sometimes', 'required', 'integer', 'exists:comunidades,id'],
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:5000'],
        ]);

        if (isset($validated['comunidad_id'])) {
            $comunidad = Comunidad::query()
                ->whereKey($validated['comunidad_id'])
                ->where('team_id', $team->id)
                ->firstOrFail();
            $validated['comunidad_id'] = $comunidad->id;
        }

        $foro->update($validated);

        return $request->expectsJson()
            ? response()->json($foro->fresh())
            : redirect()->route('foros.show', [$team->slug, $foro])->with('status', 'Foro actualizado.');
    }

    public function destroy(Request $request, string $current_team, Foro $foro): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('delete', [$foro, $team]);

        $foro->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Foro eliminado correctamente.'])
            : redirect()->route('foros.index', $team->slug)->with('status', 'Foro eliminado.');
    }
}
