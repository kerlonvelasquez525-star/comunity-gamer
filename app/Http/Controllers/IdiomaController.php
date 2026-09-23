<?php

namespace App\Http\Controllers;

use App\Models\Idioma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class IdiomaController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->list($request);
    }

    public function create(Request $request): Response
    {
        return $this->form($request, 'Nuevo idioma');
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Idioma::class, $team]);
        $item = Idioma::create($request->validate(['codigo' => 'required|string|max:5|unique:idiomas,codigo', 'nombre' => 'required|string|max:100']));

        return $request->expectsJson() ? response()->json($item, 201) : redirect()->route('idiomas.show', [$team->slug, $item]);
    }

    public function show(Request $request, string $current_team, Idioma $idioma): Response
    {
        return $this->detail($request, $idioma);
    }

    public function edit(Request $request, string $current_team, Idioma $idioma): Response
    {
        return $this->form($request, 'Editar idioma', $idioma);
    }

    public function update(Request $request, string $current_team, Idioma $idioma): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$idioma, $team]);
        $idioma->update($request->validate(['codigo' => 'required|string|max:5|unique:idiomas,codigo,'.$idioma->id, 'nombre' => 'required|string|max:100']));

        return $request->expectsJson() ? response()->json($idioma->fresh()) : redirect()->route('idiomas.show', [$team->slug, $idioma]);
    }

    public function destroy(Request $request, string $current_team, Idioma $idioma): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('delete', [$idioma, $team]);
        $idioma->delete();

        return $request->expectsJson() ? response()->json(['message' => 'Idioma eliminado']) : redirect()->route('idiomas.index', $team->slug);
    }

    private function list(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [Idioma::class, $team]);
        $items = Idioma::latest()->paginate(12);

        return $request->expectsJson() ? response()->json($items) : response()->view('gamer.index', ['resource' => 'idiomas', 'title' => 'Idiomas', 'items' => $items, 'team' => $team]);
    }

    private function form(Request $request, string $title, ?Idioma $item = null): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize($item ? 'update' : 'create', $item ? [$item, $team] : [Idioma::class, $team]);

        return response()->view('gamer.form', ['resource' => 'idiomas', 'title' => $title, 'item' => $item, 'team' => $team]);
    }

    private function detail(Request $request, Idioma $item): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$item, $team]);

        return $request->expectsJson() ? response()->json($item) : response()->view('gamer.show', ['resource' => 'idiomas', 'title' => $item->nombre, 'item' => $item, 'team' => $team]);
    }
}
