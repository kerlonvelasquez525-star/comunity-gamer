<?php

namespace App\Http\Controllers;

use App\Models\Problemas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ProblemasController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [Problemas::class, $team]);

        $problemas = Problemas::query()
            ->where('team_id', $team->id)
            ->with('autor')
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
            ->when($request->filled('prioridad'), fn ($query) => $query->where('prioridad', $request->string('prioridad')))
            ->latest()
            ->paginate($request->integer('per_page', 15))
            ->appends($request->query());

        return $request->expectsJson()
            ? response()->json($problemas)
            : response()->view('gamer.index', [
                'resource' => 'problemas',
                'title' => 'Centro de problemas',
                'items' => $problemas,
                'team' => $team,
            ]);
    }

    /**
     * Formulario publico de reporte (pages/auth/problemas.blade.php).
     */
    public function create(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Problemas::class, $team]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Formulario de reporte disponible.'])
            : response()->view('pages::auth.problemas', [
                'resource' => 'problemas',
                'title' => 'Reportar una incidencia',
                'team' => $team,
            ]);
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Problemas::class, $team]);

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:160'],
            'descripcion' => ['required', 'string', 'max:5000'],
            'prioridad' => ['required', 'in:baja,media,alta,critica'],
            'plataforma' => ['nullable', 'string', 'max:80'],
        ]);

        $problema = Problemas::create([
            ...$validated,
            'team_id' => $team->id,
            'estado' => 'abierto',
            'user_id' => $request->user()->id,
        ]);

        return $request->expectsJson()
            ? response()->json($problema, 201)
            : redirect()->route('problemas.show', [$team->slug, $problema])->with('status', 'Reporte enviado correctamente.');
    }

    public function show(Request $request, string $current_team, Problemas $problema): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$problema, $team]);

        return $request->expectsJson()
            ? response()->json($problema)
            : response()->view('gamer.show', [
                'resource' => 'problemas',
                'title' => $problema->titulo,
                'item' => $problema,
                'team' => $team,
            ]);
    }

    public function edit(Request $request, string $current_team, Problemas $problema): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$problema, $team]);

        return $request->expectsJson()
            ? response()->json($problema)
            : response()->view('gamer.form', [
                'resource' => 'problemas',
                'title' => 'Editar reporte',
                'item' => $problema,
                'team' => $team,
            ]);
    }

    public function update(Request $request, string $current_team, Problemas $problema): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$problema, $team]);

        $validated = $request->validate([
            'titulo' => ['sometimes', 'required', 'string', 'max:160'],
            'descripcion' => ['sometimes', 'required', 'string', 'max:5000'],
            'prioridad' => ['sometimes', 'required', 'in:baja,media,alta,critica'],
            'estado' => ['sometimes', 'required', 'in:abierto,en_progreso,resuelto,cerrado'],
            'plataforma' => ['nullable', 'string', 'max:80'],
        ]);

        $problema->update($validated);

        return $request->expectsJson()
            ? response()->json($problema->fresh())
            : redirect()->route('problemas.show', [$team->slug, $problema])->with('status', 'Reporte actualizado.');
    }

    public function destroy(Request $request, string $current_team, Problemas $problema): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('delete', [$problema, $team]);

        $problema->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Reporte eliminado correctamente.'])
            : redirect()->route('problemas.index', $team->slug)->with('status', 'Reporte eliminado.');
    }
}
