<?php

namespace App\Http\Controllers;

use App\Models\Comunidad;
use App\Models\ComunidadSolicitud;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicCommunityController extends Controller
{
    public function index(Request $request): View
    {
        $comunidades = Comunidad::query()
            ->with(['creador', 'equipo'])
            ->withCount('miembros')
            ->when($request->user(), fn ($query) => $query->with([
                'miembros' => fn ($members) => $members->whereKey($request->user()->id),
                'solicitudes' => fn ($requests) => $requests->where('user_id', $request->user()->id),
            ]))
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
            ->latest()
            ->paginate(12)
            ->appends($request->query());

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

        return view('public-community', [
            'items' => $comunidades,
            'title' => 'Comunidades activas',
            'filterOptions' => $filterOptions,
        ]);
    }

    public function apply(Request $request, Comunidad $comunidad): RedirectResponse
    {
        abort_if(in_array(strtolower((string) $comunidad->estado), ['cerrada', 'pausada'], true), 422, 'Esta comunidad no está recibiendo solicitudes.');

        if ($comunidad->max_miembros && $comunidad->miembros()->count() >= $comunidad->max_miembros) {
            return back()->with('status', 'La comunidad ya alcanzó su capacidad máxima.');
        }

        if ($comunidad->miembros()->whereKey($request->user()->id)->exists()) {
            return back()->with('status', 'Ya perteneces a esta comunidad.');
        }

        ComunidadSolicitud::updateOrCreate(
            ['comunidad_id' => $comunidad->id, 'user_id' => $request->user()->id],
            ['estado' => 'pendiente'],
        );

        return back()->with('status', 'Solicitud enviada. El administrador debe aprobar tu ingreso.');
    }
}
