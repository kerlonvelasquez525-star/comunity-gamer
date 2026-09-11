<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class PublicacionController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [Publicacion::class, $team]);

        $items = Publicacion::query()
            ->where('team_id', $team->id)
            ->with('autor')
            ->when($request->filled('buscar'), fn ($query) => $query->where('titulo', 'like', '%'.$request->string('buscar').'%'))
            ->latest()
            ->paginate($request->integer('per_page', 12))
            ->appends($request->query());

        return $request->expectsJson()
            ? response()->json($items)
            : response()->view('gamer.index', [
                'resource' => 'publicaciones',
                'title' => 'Publicaciones',
                'items' => $items,
                'team' => $team,
            ]);
    }

    public function create(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Publicacion::class, $team]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Formulario de creación disponible.'])
            : response()->view('gamer.form', [
                'resource' => 'publicaciones',
                'title' => 'Nueva publicación',
                'team' => $team,
            ]);
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Publicacion::class, $team]);

        $validated = $request->validate([
            'titulo' => ['sometimes', 'nullable', 'string', 'max:180'],
            'contenido' => ['required', 'string', 'max:12000'],
        ]);

        $item = Publicacion::create([
            ...$validated,
            'team_id' => $team->id,
            'user_id' => $request->user()->id,
        ]);

        return $request->expectsJson()
            ? response()->json($item->load('autor'), 201)
            : redirect()->route('publicaciones.show', [$team->slug, $item])->with('status', 'Publicación creada.');
    }

    public function show(Request $request, string $current_team, Publicacion $publicacion): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$publicacion, $team]);

        return $request->expectsJson()
            ? response()->json($publicacion->load('autor'))
            : response()->view('gamer.show', [
                'resource' => 'publicaciones',
                'title' => $publicacion->titulo ?? 'Publicación',
                'item' => $publicacion->load('autor'),
                'team' => $team,
            ]);
    }

    public function edit(Request $request, string $current_team, Publicacion $publicacion): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$publicacion, $team]);

        return $request->expectsJson()
            ? response()->json($publicacion)
            : response()->view('gamer.form', [
                'resource' => 'publicaciones',
                'title' => 'Editar publicación',
                'item' => $publicacion,
                'team' => $team,
            ]);
    }

    public function update(Request $request, string $current_team, Publicacion $publicacion): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$publicacion, $team]);

        $validated = $request->validate([
            'titulo' => ['sometimes', 'nullable', 'string', 'max:180'],
            'contenido' => ['sometimes', 'required', 'string', 'max:12000'],
        ]);

        $publicacion->update($validated);

        return $request->expectsJson()
            ? response()->json($publicacion->fresh())
            : redirect()->route('publicaciones.show', [$team->slug, $publicacion])->with('status', 'Publicación actualizada.');
    }

    public function destroy(Request $request, string $current_team, Publicacion $publicacion): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('delete', [$publicacion, $team]);

        $publicacion->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Publicación eliminada correctamente.'])
            : redirect()->route('publicaciones.index', $team->slug)->with('status', 'Publicación eliminada.');
    }
}
