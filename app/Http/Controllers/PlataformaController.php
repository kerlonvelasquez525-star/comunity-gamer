<?php

namespace App\Http\Controllers;

use App\Models\Plataforma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class PlataformaController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->list($request);
    }

    public function create(Request $request): Response
    {
        return $this->form($request, 'Nueva plataforma');
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Plataforma::class, $team]);
        $item = Plataforma::create($request->validate(['nombre' => 'required|string|max:100|unique:plataformas,nombre']));

        return $request->expectsJson() ? response()->json($item, 201) : redirect()->route('plataformas.show', [$team->slug, $item]);
    }

    public function show(Request $request, string $current_team, Plataforma $plataforma): Response
    {
        return $this->detail($request, $plataforma);
    }

    public function edit(Request $request, string $current_team, Plataforma $plataforma): Response
    {
        return $this->form($request, 'Editar plataforma', $plataforma);
    }

    public function update(Request $request, string $current_team, Plataforma $plataforma): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$plataforma, $team]);
        $plataforma->update($request->validate(['nombre' => 'required|string|max:100|unique:plataformas,nombre,'.$plataforma->id]));

        return $request->expectsJson() ? response()->json($plataforma->fresh()) : redirect()->route('plataformas.show', [$team->slug, $plataforma]);
    }

    public function destroy(Request $request, string $current_team, Plataforma $plataforma): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('delete', [$plataforma, $team]);
        $plataforma->delete();

        return $request->expectsJson() ? response()->json(['message' => 'Plataforma eliminada']) : redirect()->route('plataformas.index', $team->slug);
    }

    private function list(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [Plataforma::class, $team]);
        $items = Plataforma::latest()->paginate(12);

        return $request->expectsJson() ? response()->json($items) : response()->view('gamer.index', ['resource' => 'plataformas', 'title' => 'Plataformas', 'items' => $items, 'team' => $team]);
    }

    private function form(Request $request, string $title, ?Plataforma $item = null): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize($item ? 'update' : 'create', $item ? [$item, $team] : [Plataforma::class, $team]);

        return response()->view('gamer.form', ['resource' => 'plataformas', 'title' => $title, 'item' => $item, 'team' => $team]);
    }

    private function detail(Request $request, Plataforma $item): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$item, $team]);

        return $request->expectsJson() ? response()->json($item) : response()->view('gamer.show', ['resource' => 'plataformas', 'title' => $item->nombre, 'item' => $item, 'team' => $team]);
    }
}
