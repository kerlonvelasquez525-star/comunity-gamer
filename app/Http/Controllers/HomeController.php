<?php

namespace App\Http\Controllers;

use App\Models\Comentarios;
use App\Models\Comunidad;
use App\Models\noticias;
use App\Models\Notificacion;
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
                ->where(function ($query) use ($team): void {
                    $query->where('team_id', $team->id)
                        ->orWhere(function ($publicQuery): void {
                            $publicQuery->whereNull('team_id')
                                ->where('es_oficial', true);
                        });
                })
                ->with(['autor', 'equipo'])
                ->latest()
                ->limit(5)
                ->get(),
            'comunidades_totales' => Comunidad::query()->count(),
            'chat_users' => $team->members()
                ->whereKeyNot($request->user()->id)
                ->limit(12)
                ->get(),
            'notificaciones_recientes' => Notificacion::query()
                ->where('user_id', $request->user()->id)
                ->latest()
                ->limit(4)
                ->get(),
            'noticias_totales' => noticias::query()
                ->where(function ($query) use ($team): void {
                    $query->where('team_id', $team->id)
                        ->orWhere(function ($publicQuery): void {
                            $publicQuery->whereNull('team_id')
                                ->where('es_oficial', true);
                        });
                })
                ->count(),
            'comentarios_totales' => Comentarios::where('team_id', $team->id)->count(),
            'problemas_abiertos' => Problemas::query()
                ->whereIn('estado', ['abierto', 'en_progreso'])
                ->count(),
        ];

        return $request->expectsJson()
            ? response()->json($data)
            : response()->view('dashboard', $data);
    }
}
