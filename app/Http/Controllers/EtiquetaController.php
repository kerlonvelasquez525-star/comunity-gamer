<?php

namespace App\Http\Controllers;

use App\Models\Etiqueta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class EtiquetaController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->list($request);
    }

    public function create(Request $request): Response
    {
        return $this->form($request, 'Nueva etiqueta');
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Etiqueta::class, $team]);
        $item = Etiqueta::create($request->validate(['nombre' => 'required|string|max:100|unique:etiquetas,nombre']));

        return $request->expectsJson() ? response()->json($item, 201) : redirect()->route('etiquetas.show', [$team->slug, $item]);
    }

    public function show(Request $request, string $current_team, Etiqueta $etiqueta): Response
    {
        return $this->detail($request, $etiqueta);
    }

    public function edit(Request $request, string $current_team, Etiqueta $etiqueta): Response
    {
        return $this->form($request, 'Editar etiqueta', $etiqueta);
    }

    public function update(Request $request, string $current_team, Etiqueta $etiqueta): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$etiqueta, $team]);
        $etiqueta->update($request->validate(['nombre' => 'required|string|max:100|unique:etiquetas,nombre,'.$etiqueta->id_etiqueta]));

        return $request->expectsJson() ? response()->json($etiqueta->fresh()) : redirect()->route('etiquetas.show', [$team->slug, $etiqueta]);
    }

    public function destroy(Request $request, string $current_team, Etiqueta $etiqueta): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('delete', [$etiqueta, $team]);
        $etiqueta->delete();

        return $request->expectsJson() ? response()->json(['message' => 'Etiqueta eliminada']) : redirect()->route('etiquetas.index', $team->slug);
    }

    private function list(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [Etiqueta::class, $team]);
        $items = Etiqueta::latest()->paginate(12);

        return $request->expectsJson() ? response()->json($items) : response()->view('gamer.index', ['resource' => 'etiquetas', 'title' => 'Etiquetas', 'items' => $items, 'team' => $team]);
    }

    private function form(Request $request, string $title, ?Etiqueta $item = null): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize($item ? 'update' : 'create', $item ? [$item, $team] : [Etiqueta::class, $team]);

        return response()->view('gamer.form', ['resource' => 'etiquetas', 'title' => $title, 'item' => $item, 'team' => $team]);
    }

    private function detail(Request $request, Etiqueta $item): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$item, $team]);

        return $request->expectsJson() ? response()->json($item) : response()->view('gamer.show', ['resource' => 'etiquetas', 'title' => $item->nombre, 'item' => $item, 'team' => $team]);
    }
}
