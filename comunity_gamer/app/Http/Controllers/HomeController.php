<?php

namespace App\Http\Controllers;

use App\Models\Comentarios;
use App\Models\Comunidad;
use App\Models\noticias;
use App\Models\Problemas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [noticias::class, $team]);

        $data = [
            'noticias_recientes' => noticias::where('team_id', $team->id)->latest()->limit(5)->get(),
            'comunidades_activas' => Comunidad::where('team_id', $team->id)->latest()->limit(5)->get(),
            'problemas_abiertos' => Problemas::where('team_id', $team->id)->whereIn('estado', ['abierto', 'en_progreso'])->count(),
            'comentarios_totales' => Comentarios::where('team_id', $team->id)->count(),
            'team' => $team,
        ];

        return $request->expectsJson()
            ? response()->json($data)
            : response()->view('gamer.home', $data);
    }

    public function create(Request $request): Response { return $this->readonlyResponse(); }
    public function store(Request $request): Response { return $this->readonlyResponse(); }
    public function show(Request $request): Response { return $this->index($request); }
    public function edit(Request $request): Response { return $this->readonlyResponse(); }
    public function update(Request $request): Response { return $this->readonlyResponse(); }
    public function destroy(Request $request): Response { return $this->readonlyResponse(); }

    private function readonlyResponse(): Response
    {
        return response()->json(['message' => 'La portada es de solo lectura.'], 405);
    }
}