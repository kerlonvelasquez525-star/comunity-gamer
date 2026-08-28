<?php

namespace App\Http\Controllers;

use App\Models\noticias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class NoticiasController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [noticias::class, $team]);

        $noticias = noticias::query()
            ->where('team_id', $team->id)
            ->with('autor')
            ->when($request->filled('buscar'), fn ($query) => $query->where('titulo', 'like', '%'.$request->string('buscar').'%'))
            ->when($request->filled('categoria'), fn ($query) => $query->where('categoria', $request->string('categoria')))
            ->latest()
            ->paginate($request->integer('per_page', 12))
            ->appends($request->query());

        return $request->expectsJson()
            ? response()->json($noticias)
            : response()->view('pages::auth.noticias', [
                'resource' => 'noticias',
                'title' => 'Centro de noticias',
                'items' => $noticias,
                'team' => $team,
            ]);
    }

    public function create(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [noticias::class, $team]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Formulario de creacion disponible.'])
            : response()->view('gamer.form', [
                'resource' => 'noticias',
                'title' => 'Nueva noticia',
                'team' => $team,
            ]);
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [noticias::class, $team]);

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:180'],
            'contenido' => ['required', 'string', 'max:10000'],
            'categoria' => ['nullable', 'string', 'max:80'],
            'imagen_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $noticia = noticias::create([
            ...$validated,
            'team_id' => $team->id,
            'user_id' => $request->user()->id,
        ]);

        return $request->expectsJson()
            ? response()->json($noticia, 201)
            : redirect()->route('noticias.show', [$team->slug, $noticia])->with('status', 'Noticia publicada.');
    }

    public function show(Request $request, string $current_team, noticias $noticia): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$noticia, $team]);

        return $request->expectsJson()
            ? response()->json($noticia)
            : response()->view('gamer.show', [
                'resource' => 'noticias',
                'title' => $noticia->titulo,
                'item' => $noticia,
                'team' => $team,
            ]);
    }

    public function edit(Request $request, string $current_team, noticias $noticia): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$noticia, $team]);

        return $request->expectsJson()
            ? response()->json($noticia)
            : response()->view('gamer.form', [
                'resource' => 'noticias',
                'title' => 'Editar noticia',
                'item' => $noticia,
                'team' => $team,
            ]);
    }

    public function update(Request $request, string $current_team, noticias $noticia): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$noticia, $team]);

        $validated = $request->validate([
            'titulo' => ['sometimes', 'required', 'string', 'max:180'],
            'contenido' => ['sometimes', 'required', 'string', 'max:10000'],
            'categoria' => ['nullable', 'string', 'max:80'],
            'imagen_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $noticia->update($validated);

        return $request->expectsJson()
            ? response()->json($noticia->fresh())
            : redirect()->route('noticias.show', [$team->slug, $noticia])->with('status', 'Noticia actualizada.');
    }

    public function destroy(Request $request, string $current_team, noticias $noticia): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('delete', [$noticia, $team]);

        $noticia->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Noticia eliminada correctamente.'])
            : redirect()->route('noticias.index', $team->slug)->with('status', 'Noticia eliminada.');
    }
}
