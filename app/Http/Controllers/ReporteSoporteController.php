<?php

namespace App\Http\Controllers;

use App\Models\ReporteSoporte;
use App\Models\RespuestaSoporte;
use App\Models\VotoSoporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ReporteSoporteController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [ReporteSoporte::class, $team]);
        $items = ReporteSoporte::with(['usuario', 'etiquetas'])->latest('fecha_creacion')->paginate(20);

        return $request->expectsJson() ? response()->json($items) : response()->view('gamer.index', ['resource' => 'reportes-soporte', 'title' => 'Reportes de soporte', 'items' => $items, 'team' => $team]);
    }

    public function create(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [ReporteSoporte::class, $team]);

        return response()->view('gamer.form', ['resource' => 'reportes-soporte', 'title' => 'Nuevo reporte', 'team' => $team]);
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [ReporteSoporte::class, $team]);
        $data = $request->validate(['asunto' => 'required|string|max:150', 'descripcion' => 'required|string', 'etiquetas' => 'nullable|array', 'etiquetas.*' => 'integer|exists:etiquetas,id_etiqueta']);
        $item = ReporteSoporte::create(['id_usuario' => $request->user()->id, 'asunto' => $data['asunto'], 'descripcion' => $data['descripcion'], 'fecha_creacion' => now()]);
        $item->etiquetas()->sync($data['etiquetas'] ?? []);

        return $request->expectsJson() ? response()->json($item->load('etiquetas'), 201) : redirect()->route('reportes-soporte.show', [$team->slug, $item]);
    }

    public function show(Request $request, string $current_team, ReporteSoporte $reporteSoporte): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$reporteSoporte, $team]);
        $item = $reporteSoporte->load(['usuario', 'etiquetas', 'respuestas.usuario', 'votos']);

        return $request->expectsJson() ? response()->json($item) : response()->view('gamer.show', ['resource' => 'reportes-soporte', 'title' => $item->asunto, 'item' => $item, 'team' => $team]);
    }

    public function edit(Request $request, string $current_team, ReporteSoporte $reporteSoporte): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$reporteSoporte, $team]);

        return response()->view('gamer.form', ['resource' => 'reportes-soporte', 'title' => 'Editar reporte', 'item' => $reporteSoporte, 'team' => $team]);
    }

    public function update(Request $request, string $current_team, ReporteSoporte $reporteSoporte): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$reporteSoporte, $team]);
        $data = $request->validate(['asunto' => 'sometimes|required|string|max:150', 'descripcion' => 'sometimes|required|string', 'estado' => 'sometimes|required|in:abierto,en_proceso,cerrado', 'etiquetas' => 'nullable|array', 'etiquetas.*' => 'integer|exists:etiquetas,id_etiqueta']);
        $reporteSoporte->update(collect($data)->except('etiquetas')->all());
        if (array_key_exists('etiquetas', $data)) {
            $reporteSoporte->etiquetas()->sync($data['etiquetas'] ?? []);
        }

        return $request->expectsJson() ? response()->json($reporteSoporte->fresh()) : redirect()->route('reportes-soporte.show', [$team->slug, $reporteSoporte]);
    }

    public function destroy(Request $request, string $current_team, ReporteSoporte $reporteSoporte): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('delete', [$reporteSoporte, $team]);
        $reporteSoporte->delete();

        return $request->expectsJson() ? response()->json(['message' => 'Reporte eliminado']) : redirect()->route('reportes-soporte.index', $team->slug);
    }

    public function responder(Request $request, string $current_team, ReporteSoporte $reporteSoporte): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('respond', [$reporteSoporte, $team]);
        $item = RespuestaSoporte::create(['id_reporte' => $reporteSoporte->id_reporte, 'id_usuario' => $request->user()->id, 'mensaje' => $request->validate(['mensaje' => 'required|string|max:10000'])['mensaje'], 'fecha_respuesta' => now()]);

        return $request->expectsJson() ? response()->json($item, 201) : back();
    }

    public function votar(Request $request, string $current_team, ReporteSoporte $reporteSoporte): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('vote', [$reporteSoporte, $team]);
        $item = VotoSoporte::updateOrCreate(['id_reporte' => $reporteSoporte->id_reporte, 'id_usuario' => $request->user()->id], ['voto' => (int) $request->validate(['voto' => 'required|in:-1,1'])['voto']]);

        return $request->expectsJson() ? response()->json($item) : back();
    }
}
