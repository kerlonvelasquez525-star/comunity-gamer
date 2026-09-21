<?php

namespace App\Http\Controllers;

use App\Models\Juego;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class JuegoController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->list($request);
    }

    public function create(Request $request): Response
    {
        return $this->form($request, 'Nuevo juego');
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Juego::class, $team]);
        $data = $request->validate(['titulo' => 'required|string|max:150', 'descripcion' => 'nullable|string', 'desarrollador' => 'nullable|string|max:150', 'fecha_lanzamiento' => 'nullable|date', 'plataformas' => 'nullable|array', 'plataformas.*' => 'integer|exists:plataformas,id', 'idiomas' => 'nullable|array', 'idiomas.*' => 'integer|exists:idiomas,id']);
        $item = Juego::create(array_diff_key($data, array_flip(['plataformas', 'idiomas'])));
        $item->plataformas()->sync($data['plataformas'] ?? []);
        $item->idiomas()->sync($data['idiomas'] ?? []);

        return $request->expectsJson() ? response()->json($item->load(['plataformas', 'idiomas']), 201) : redirect()->route('juegos.show', [$team->slug, $item]);
    }

    public function show(Request $request, string $current_team, Juego $juego): Response
    {
        return $this->detail($request, $juego);
    }

    public function edit(Request $request, string $current_team, Juego $juego): Response
    {
        return $this->form($request, 'Editar juego', $juego);
    }

    public function update(Request $request, string $current_team, Juego $juego): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$juego, $team]);
        $data = $request->validate(['titulo' => 'sometimes|required|string|max:150', 'descripcion' => 'nullable|string', 'desarrollador' => 'nullable|string|max:150', 'fecha_lanzamiento' => 'nullable|date', 'plataformas' => 'nullable|array', 'plataformas.*' => 'integer|exists:plataformas,id', 'idiomas' => 'nullable|array', 'idiomas.*' => 'integer|exists:idiomas,id']);
        $juego->update(array_diff_key($data, array_flip(['plataformas', 'idiomas'])));
        if (array_key_exists('plataformas', $data)) {
            $juego->plataformas()->sync($data['plataformas'] ?? []);
        } if (array_key_exists('idiomas', $data)) {
            $juego->idiomas()->sync($data['idiomas'] ?? []);
        }

        return $request->expectsJson() ? response()->json($juego->fresh()->load(['plataformas', 'idiomas'])) : redirect()->route('juegos.show', [$team->slug, $juego]);
    }

    public function destroy(Request $request, string $current_team, Juego $juego): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('delete', [$juego, $team]);
        $juego->delete();

        return $request->expectsJson() ? response()->json(['message' => 'Juego eliminado']) : redirect()->route('juegos.index', $team->slug);
    }

    private function list(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [Juego::class, $team]);
        $items = Juego::with(['plataformas', 'idiomas'])->latest()->paginate(12);

        return $request->expectsJson() ? response()->json($items) : response()->view('gamer.index', ['resource' => 'juegos', 'title' => 'Juegos', 'items' => $items, 'team' => $team]);
    }

    private function form(Request $request, string $title, ?Juego $item = null): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize($item ? 'update' : 'create', $item ? [$item, $team] : [Juego::class, $team]);

        return response()->view('gamer.form', ['resource' => 'juegos', 'title' => $title, 'item' => $item, 'team' => $team]);
    }

    private function detail(Request $request, Juego $item): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$item, $team]);

        return $request->expectsJson() ? response()->json($item->load(['plataformas', 'idiomas'])) : response()->view('gamer.show', ['resource' => 'juegos', 'title' => $item->titulo, 'item' => $item, 'team' => $team]);
    }
}
