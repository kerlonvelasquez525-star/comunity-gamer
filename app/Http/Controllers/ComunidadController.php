<?php

namespace App\Http\Controllers;

use App\Models\Comunidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ComunidadController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [Comunidad::class, $team]);

        $comunidades = Comunidad::query()
            ->where('team_id', $team->id)
            ->when($request->filled('buscar'), fn ($query) => $query->where('nombre', 'like', '%'.$request->string('buscar').'%'))
            ->with('creador')
            ->withCount('miembros')
            ->latest()
            ->paginate($request->integer('per_page', 12))
            ->appends($request->query());

        return $request->expectsJson()
            ? response()->json($comunidades)
            : response()->view('pages::auth.comunidad', [
                'resource' => 'comunidad',
                'title' => 'Comunidades gamer',
                'items' => $comunidades,
                'team' => $team,
            ]);
    }

    public function create(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Comunidad::class, $team]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Formulario de creacion disponible.'])
            : response()->view('gamer.form', [
                'resource' => 'comunidad',
                'title' => 'Nueva comunidad',
                'team' => $team,
            ]);
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Comunidad::class, $team]);

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
        ]);

        $comunidad = DB::transaction(function () use ($request, $team, $validated) {
            $comunidad = Comunidad::create([
                ...$validated,
                'team_id' => $team->id,
                'creador_id' => $request->user()->id,
            ]);

            $comunidad->miembros()->syncWithoutDetaching([
                $request->user()->id => ['rol' => 'admin'],
            ]);

            return $comunidad;
        });

        return $request->expectsJson()
            ? response()->json($comunidad, 201)
            : redirect()->route('comunidad.show', [$team->slug, $comunidad])->with('status', 'Comunidad creada.');
    }

    public function show(Request $request, string $current_team, Comunidad $comunidad): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$comunidad, $team]);

        $comunidad->loadCount('miembros');

        return $request->expectsJson()
            ? response()->json($comunidad)
            : response()->view('gamer.show', [
                'resource' => 'comunidad',
                'title' => $comunidad->nombre,
                'item' => $comunidad,
                'team' => $team,
            ]);
    }

    public function edit(Request $request, string $current_team, Comunidad $comunidad): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$comunidad, $team]);

        return $request->expectsJson()
            ? response()->json($comunidad)
            : response()->view('gamer.form', [
                'resource' => 'comunidad',
                'title' => 'Editar comunidad',
                'item' => $comunidad,
                'team' => $team,
            ]);
    }

    public function update(Request $request, string $current_team, Comunidad $comunidad): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$comunidad, $team]);

        $validated = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
        ]);

        $comunidad->update($validated);

        return $request->expectsJson()
            ? response()->json($comunidad->fresh())
            : redirect()->route('comunidad.show', [$team->slug, $comunidad])->with('status', 'Comunidad actualizada.');
    }

    public function destroy(Request $request, string $current_team, Comunidad $comunidad): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('delete', [$comunidad, $team]);

        $comunidad->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Comunidad archivada correctamente.'])
            : redirect()->route('comunidad.index', $team->slug)->with('status', 'Comunidad archivada.');
    }
}
