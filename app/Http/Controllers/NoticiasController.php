<?php

namespace App\Http\Controllers;

use App\Models\noticias;
use App\Models\NoticiasComentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controlador encargado de gestionar las noticias internas del equipo.
 *
 * Aquí se realizan las operaciones de listado, creación, visualización,
 * edición y eliminación de contenido informativo asociado a un equipo.
 */
class NoticiasController extends Controller
{
    /**
     * Lista las noticias del equipo actual con filtros y paginación.
     */
    public function index(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [noticias::class, $team]);

        $noticias = noticias::query()
            ->where(fn ($query) => $query->where('team_id', $team->id)->orWhereNull('team_id'))
            ->with(['autor', 'equipo', 'comentarios' => fn ($query) => $query->with('autor')->latest()])
            ->when($request->filled('buscar'), fn ($query) => $query->where('titulo', 'like', '%'.$request->string('buscar').'%'))
            ->when($request->filled('categoria'), fn ($query) => $query->where('categoria', $request->string('categoria')))
            ->when($request->filled('oficial'), fn ($query) => $query->where('es_oficial', true))
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

    /**
     * Muestra el formulario para crear una nueva noticia.
     */
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

    /**
     * Guarda una noticia nueva con validación y posible imagen adjunta.
     */
    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [noticias::class, $team]);

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:180'],
            'contenido' => ['required', 'string', 'max:10000'],
            'categoria' => ['nullable', 'string', 'max:80'],
            'imagen_url' => ['nullable', 'url', 'max:2048'],
            'imagen' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
        ]);

        if ($request->hasFile('imagen')) {
            $validated['imagen_url'] = $request->file('imagen')->store('noticias', 'public');
        }

        unset($validated['imagen']);

        $noticia = noticias::create([
            ...$validated,
            'team_id' => $team->id,
            'user_id' => $request->user()->id,
        ]);

        return $request->expectsJson()
            ? response()->json($noticia, 201)
            : redirect()->route('noticias.show', [$team->slug, $noticia])->with('status', 'Noticia publicada.');
    }

    /**
     * Muestra el detalle completo de una noticia específica.
     */
    public function show(Request $request, string $current_team, noticias $noticia): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$noticia, $team]);

        $noticia->load(['comentarios' => fn ($query) => $query->with('autor')->latest()]);

        return $request->expectsJson()
            ? response()->json($noticia)
            : response()->view('gamer.show', [
                'resource' => 'noticias',
                'title' => $noticia->titulo,
                'item' => $noticia,
                'team' => $team,
            ]);
    }

    public function storeComment(Request $request, string $current_team, noticias $noticia): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$noticia, $team]);

        $validated = $request->validate([
            'contenido' => ['required', 'string', 'max:2000'],
        ]);

        $comentario = NoticiasComentario::create([
            'noticia_id' => $noticia->id,
            'user_id' => $request->user()->id,
            'contenido' => $validated['contenido'],
        ]);

        return $request->expectsJson()
            ? response()->json($comentario, 201)
            : redirect()->route('noticias.show', [$team->slug, $noticia])->with('status', 'Comentario publicado.');
    }

    public function updateComment(Request $request, string $current_team, noticias $noticia, NoticiasComentario $comentario): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$noticia, $team]);
        abort_unless($comentario->noticia_id === $noticia->id && $comentario->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'contenido' => ['required', 'string', 'max:2000'],
        ]);

        $comentario->update($validated);

        return $request->expectsJson()
            ? response()->json($comentario->fresh()->load('autor'))
            : back()->with('status', 'Comentario actualizado.');
    }

    public function destroyComment(Request $request, string $current_team, noticias $noticia, NoticiasComentario $comentario): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$noticia, $team]);
        abort_unless($comentario->noticia_id === $noticia->id && $comentario->user_id === $request->user()->id, 403);

        $comentario->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Comentario eliminado correctamente.'])
            : back()->with('status', 'Comentario eliminado.');
    }

    /**
     * Presenta el formulario para editar una noticia ya creada.
     */
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

    /**
     * Actualiza la noticia con los datos validados y reemplaza la imagen si se envía una nueva.
     */
    public function update(Request $request, string $current_team, noticias $noticia): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$noticia, $team]);

        $validated = $request->validate([
            'titulo' => ['sometimes', 'required', 'string', 'max:180'],
            'contenido' => ['sometimes', 'required', 'string', 'max:10000'],
            'categoria' => ['nullable', 'string', 'max:80'],
            'imagen_url' => ['nullable', 'url', 'max:2048'],
            'imagen' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
        ]);

        if ($request->hasFile('imagen')) {
            if ($noticia->imagen_url && ! filter_var($noticia->imagen_url, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($noticia->imagen_url);
            }

            $validated['imagen_url'] = $request->file('imagen')->store('noticias', 'public');
        }

        unset($validated['imagen']);

        $noticia->update($validated);

        return $request->expectsJson()
            ? response()->json($noticia->fresh())
            : redirect()->route('noticias.show', [$team->slug, $noticia])->with('status', 'Noticia actualizada.');
    }

    /**
     * Elimina definitivamente una noticia del equipo.
     */
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
