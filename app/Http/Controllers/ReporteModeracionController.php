<?php

namespace App\Http\Controllers;

use App\Models\ReporteModeracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ReporteModeracionController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [ReporteModeracion::class, $team]);
        $items = ReporteModeracion::where('team_id', $team->id)
            ->with(['reportador', 'reportado'])
            ->latest('fecha')
            ->paginate(20);

        return $request->expectsJson() ? response()->json($items) : response()->view('gamer.index', ['resource' => 'reportes-moderacion', 'title' => 'Reportes de moderación', 'items' => $items, 'team' => $team]);
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [ReporteModeracion::class, $team]);
        $item = ReporteModeracion::create(['team_id' => $team->id, 'id_reportador' => $request->user()->id, ...$request->validate(['id_reportado' => 'required|integer|exists:users,id', 'motivo' => 'required|string|max:5000']), 'fecha' => now()]);

        return $request->expectsJson() ? response()->json($item, 201) : back()->with('status', 'Reporte enviado.');
    }

    public function show(Request $request, string $current_team, ReporteModeracion $reporteModeracion): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$reporteModeracion, $team]);

        return $request->expectsJson() ? response()->json($reporteModeracion->load(['reportador', 'reportado'])) : response()->view('gamer.show', ['resource' => 'reportes-moderacion', 'title' => 'Reporte de moderación', 'item' => $reporteModeracion->load(['reportador', 'reportado']), 'team' => $team]);
    }

    public function updateEstado(Request $request, string $current_team, ReporteModeracion $reporteModeracion): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$reporteModeracion, $team]);
        $reporteModeracion->update($request->validate(['estado' => 'required|in:pendiente,revisado,rechazado']));

        return $request->expectsJson() ? response()->json($reporteModeracion->fresh()) : back();
    }
}
