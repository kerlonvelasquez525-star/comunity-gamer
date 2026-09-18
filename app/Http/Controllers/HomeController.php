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

        $communityQuery = Comunidad::query()
            ->with(['creador', 'equipo'])
            ->withCount('miembros')
            ->when($request->filled('buscar'), fn ($query) => $query->where('nombre', 'like', '%'.$request->string('buscar').'%'))
            ->when($request->filled('juego_principal'), fn ($query) => $query->where('juego_principal', $request->string('juego_principal')))
            ->when($request->filled('plataforma'), fn ($query) => $query->where('plataforma', $request->string('plataforma')))
            ->when($request->filled('region'), fn ($query) => $query->where('region', $request->string('region')))
            ->when($request->filled('idioma'), fn ($query) => $query->where('idioma', $request->string('idioma')))
            ->when($request->filled('modalidad'), fn ($query) => $query->where('modalidad', $request->string('modalidad')))
            ->when($request->filled('horario'), fn ($query) => $query->where('horario', $request->string('horario')))
            ->when($request->filled('tipo'), fn ($query) => $query->where('tipo', $request->string('tipo')))
            ->when($request->filled('nivel'), fn ($query) => $query->where('nivel', $request->string('nivel')))
            ->when($request->filled('rango'), fn ($query) => $query->where('rango', $request->string('rango')))
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
            ->orderByDesc('miembros_count');

        $filterCatalog = [
            'juego_principal' => ['Fortnite', 'Valorant', 'Minecraft', 'Apex Legends', 'League of Legends', 'Call of Duty', 'GTA V'],
            'plataforma' => ['PC', 'PlayStation', 'Xbox', 'Nintendo Switch', 'Móvil', 'Multiplataforma'],
            'region' => ['LATAM', 'Norteamérica', 'Europa', 'Brasil', 'Asia', 'Global'],
            'idioma' => ['Español', 'Inglés', 'Portugués', 'Francés', 'Multilingüe'],
            'modalidad' => ['Casual', 'Competitiva', 'Ranked', 'Cooperativa', 'Social'],
            'horario' => ['Mañana', 'Tarde', 'Noche', 'Fines de semana', 'Flexible'],
            'tipo' => ['publica', 'privada', 'cerrada'],
            'nivel' => ['Casual', 'Competitivo', 'Pro gamer'],
            'rango' => ['Sin rango', 'Bronce', 'Plata', 'Oro', 'Platino', 'Diamante', 'Maestro'],
            'estado' => ['Abierta', 'Cerrada', 'En revisión'],
        ];

        $filterOptions = collect($filterCatalog)->mapWithKeys(
            fn (array $defaults, string $column) => [$column => collect($defaults)
                ->merge(Comunidad::query()
                    ->whereNotNull($column)
                    ->where($column, '!=', '')
                    ->distinct()
                    ->orderBy($column)
                    ->pluck($column))
                ->filter()
                ->unique()
                ->values()],
        );

        $data = [
            'team' => $team,
            'noticias_recientes' => noticias::query()
                ->where('team_id', $team->id)
                ->with(['autor', 'equipo'])
                ->latest()
                ->limit(5)
                ->get(),
            'comunidades_activas' => $communityQuery->limit(6)->get(),
            'filterOptions' => $filterOptions,
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
