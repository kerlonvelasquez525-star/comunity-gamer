<?php

namespace App\Http\Controllers;

use App\Models\Canal;
use App\Models\Mensaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class MensajeController extends Controller
{
    public function index(Request $request): Response
    {
        $team = $this->currentTeam($request);

        $canal = Canal::query()
            ->whereHas('comunidad', fn ($query) => $query->where('team_id', $team->id))
            ->whereKey($request->route('canal'))
            ->firstOrFail();

        Gate::authorize('viewAny', [Mensaje::class, $team]);

        $items = Mensaje::query()
            ->where('canal_id', $canal->id_canal)
            ->with('autor')
            ->latest()
            ->paginate($request->integer('per_page', 20))
            ->appends($request->query());

        return $request->expectsJson()
            ? response()->json($items)
            : response()->view('gamer.index', [
                'resource' => 'mensajes',
                'title' => 'Mensajes',
                'items' => $items,
                'team' => $team,
            ]);
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);

        $canal = Canal::query()
            ->whereHas('comunidad', fn ($query) => $query->where('team_id', $team->id))
            ->whereKey($request->route('canal'))
            ->firstOrFail();

        Gate::authorize('create', [Mensaje::class, $team]);

        $validated = $request->validate([
            'contenido' => ['required', 'string', 'max:8000'],
        ]);

        $mensaje = Mensaje::create([
            'id' => (string) Str::uuid(),
            'canal_id' => $canal->id_canal,
            'user_id' => $request->user()->id,
            'contenido' => $validated['contenido'],
        ]);

        return $request->expectsJson()
            ? response()->json($mensaje->load('autor'), 201)
            : back()->with('status', 'Mensaje enviado.');
    }

    public function destroy(Request $request, string $current_team, Mensaje $mensaje): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('delete', [$mensaje, $team]);

        $mensaje->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Mensaje eliminado correctamente.'])
            : back()->with('status', 'Mensaje eliminado.');
    }
}
