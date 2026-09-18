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
    /**
     * Panel principal del equipo: metricas y ultimos movimientos.
     */
    public function index(Request $request): Response
    {
        $team = $this->currentTeam($request);

        Gate::authorize('viewAny', [noticias::class, $team]);

        $data = [
            'team' => $team,
            'noticias_recientes' => noticias::query()
                ->where('team_id', $team->id)
                ->with(['autor', 'equipo'])
                ->latest()
                ->limit(5)
                ->get(),
            'comunidades_activas' => Comunidad::query()
                ->with('equipo')
                ->withCount('miembros')
                ->latest()
                ->limit(5)
                ->get(),
            'comunidades_totales' => Comunidad::query()->count(),
            'chat_users' => $team->members()
                ->whereKeyNot($request->user()->id)
                ->limit(12)
                ->get(),
            'noticias_totales' => noticias::where('team_id', $team->id)->count(),
            'comentarios_totales' => Comentarios::where('team_id', $team->id)->count(),
            'problemas_abiertos' => Problemas::query()
                ->where('team_id', $team->id)
                ->whereIn('estado', ['abierto', 'en_progreso'])
                ->count(),
        ];

        return $request->expectsJson()
            ? response()->json($data)
            : response()->view('dashboard', $data);
    }
}
