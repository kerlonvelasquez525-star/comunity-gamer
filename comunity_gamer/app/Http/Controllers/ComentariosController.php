<?php

namespace App\Http\Controllers;

use App\Models\Comentarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ComentariosController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [Comentarios::class, $team]);

        $comentarios = Comentarios::query()
            ->where('team_id', $team->id)
            ->with('autor')
            ->when($request->filled('publicacion_id'), fn ($query) => $query->where('id_publicacion', $request->integer('publicacion_id')))
            ->latest('fecha_comentario')
            ->paginate($request->integer('per_page', 20))
            ->appends($request->query());

        return $request->expectsJson()
            ? response()->json($comentarios)
            : response()->view('pages::auth.comentarios', [
                'resource' => 'comentarios',
                'title' => 'Comentarios de la comunidad',
                'items' => $comentarios,
                'team' => $team,
            ]);
    }

    public function create(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Comentarios::class, $team]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Formulario de creacion disponible.'])
            : response()->view('gamer.form', [
                'resource' => 'comentarios',
                'title' => 'Nuevo comentario',
                'team' => $team,
            ]);
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Comentarios::class, $team]);

        $validated = $request->validate([
            'id_publicacion' => ['required', 'integer', 'exists:publicaciones,id'],
            'contenido' => ['required', 'string', 'max:2000'],
        ]);

        abort_unless(
            DB::table('publicaciones')
                ->where('id', $validated['id_publicacion'])
                ->where('team_id', $team->id)
                ->exists(),
            404,
            'La publicacion solicitada no pertenece a este equipo.'
        );

        $comentario = Comentarios::create([
            ...$validated,
            'team_id' => $team->id,
            'id_usuario' => $request->user()->id,
        ]);

        return $request->expectsJson()
            ? response()->json($comentario, 201)
            : redirect()->route('comentarios.show', [$team->slug, $comentario])->with('status', 'Comentario publicado.');
    }

    public function show(Request $request, string $current_team, Comentarios $comentario): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$comentario, $team]);

        return $request->expectsJson()
            ? response()->json($comentario)
            : response()->view('gamer.show', [
                'resource' => 'comentarios',
                'title' => 'Comentario',
                'item' => $comentario,
                'team' => $team,
            ]);
    }

    public function edit(Request $request, string $current_team, Comentarios $comentario): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$comentario, $team]);

        return $request->expectsJson()
            ? response()->json($comentario)
            : response()->view('gamer.form', [
                'resource' => 'comentarios',
                'title' => 'Editar comentario',
                'item' => $comentario,
                'team' => $team,
            ]);
    }

    public function update(Request $request, string $current_team, Comentarios $comentario): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$comentario, $team]);

        $validated = $request->validate([
            'contenido' => ['sometimes', 'required', 'string', 'max:2000'],
        ]);

        $comentario->update($validated);

        return $request->expectsJson()
            ? response()->json($comentario->fresh())
            : redirect()->route('comentarios.show', [$team->slug, $comentario])->with('status', 'Comentario actualizado.');
    }

    public function destroy(Request $request, string $current_team, Comentarios $comentario): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('delete', [$comentario, $team]);

        $comentario->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Comentario eliminado correctamente.'])
            : redirect()->route('comentarios.index', $team->slug)->with('status', 'Comentario eliminado.');
    }
}
