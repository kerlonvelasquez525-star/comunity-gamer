<?php

namespace App\Http\Controllers;

use App\Models\Comunidad;
use App\Models\ComunidadSolicitud;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ComunidadController extends Controller
{
    public function settingsCommunities(Request $request): Response
    {
        $team = $request->user()?->currentTeam;
        if (! $team) {
            abort(403, 'Debes seleccionar un equipo antes de crear una comunidad.');
        }
        Gate::authorize('create', [Comunidad::class, $team]);

        return response()->view('pages.settings.communities', [
            'team' => $team,
        ]);
    }

    public function index(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('viewAny', [Comunidad::class, $team]);

        $comunidades = Comunidad::query()
            ->where(function ($query) use ($team): void {
                $query->where('team_id', $team->id)
                    ->orWhere(function ($publicQuery): void {
                        $publicQuery->where('tipo', 'publica')
                            ->whereIn('estado', ['Abierta', 'abierta']);
                    });
            })
            ->when($request->filled('buscar'), fn ($query) => $query->where('nombre', 'like', '%'.$request->string('buscar').'%'))
            ->when($request->filled('juego_principal'), fn ($query) => $query->where('juego_principal', $request->string('juego_principal')))
            ->when($request->filled('plataforma'), fn ($query) => $query->where('plataforma', $request->string('plataforma')))
            ->when($request->filled('region'), fn ($query) => $query->where('region', $request->string('region')))
            ->when($request->filled('modalidad'), fn ($query) => $query->where('modalidad', $request->string('modalidad')))
            ->when($request->filled('idioma'), fn ($query) => $query->where('idioma', $request->string('idioma')))
            ->when($request->filled('horario'), fn ($query) => $query->where('horario', $request->string('horario')))
            ->when($request->filled('tipo'), fn ($query) => $query->where('tipo', $request->string('tipo')))
            ->when($request->filled('nivel'), fn ($query) => $query->where('nivel', $request->string('nivel')))
            ->when($request->filled('rango'), fn ($query) => $query->where('rango', $request->string('rango')))
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
            ->with('creador')
            ->withCount('miembros')
            ->orderByDesc('miembros_count')
            ->paginate($request->integer('per_page', 12))
            ->appends($request->query());

        return $request->expectsJson()
            ? response()->json($comunidades)
            : response()->view('pages::auth.comunidad', [
                'resource' => 'comunidad',
                'title' => 'Comunidades gamer',
                'items' => $comunidades,
                'team' => $team,
            ]);
    }

    public function create(Request $request): Response
    {
        return $request->expectsJson()
            ? response()->json(['message' => 'La creación de comunidades está disponible desde Configuración.'])
            : redirect()->route('communities.settings');
    }

    public function store(Request $request): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('create', [Comunidad::class, $team]);

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'imagen' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
            'imagen_url' => ['nullable', 'url', 'max:2048'],
            'juego_principal' => ['nullable', 'string', 'max:100'],
            'plataforma' => ['nullable', 'string', 'max:50'],
            'region' => ['nullable', 'string', 'max:50'],
            'idioma' => ['nullable', 'string', 'max:50'],
            'modalidad' => ['nullable', 'string', 'max:50'],
            'horario' => ['nullable', 'string', 'max:50'],
            'tipo' => ['nullable', 'string', 'max:50'],
            'nivel' => ['nullable', 'string', 'max:50'],
            'rango' => ['nullable', 'string', 'max:50'],
            'estado' => ['nullable', 'string', 'max:50'],
            'max_miembros' => ['nullable', 'integer', 'min:2', 'max:500'],
        ]);

        if ($request->hasFile('imagen')) {
            $validated['imagen_url'] = $request->file('imagen')->store('comunidades', 'public');
        }

        unset($validated['imagen']);

        $comunidad = DB::transaction(function () use ($request, $team, $validated) {
            $comunidad = Comunidad::create([
                ...$validated,
                'team_id' => $team->id,
                'creador_id' => $request->user()->id,
            ]);

            $comunidad->miembros()->syncWithoutDetaching([
                $request->user()->id => ['rol' => 'admin'],
            ]);

            return $comunidad;
        });

        return $request->expectsJson()
            ? response()->json($comunidad, 201)
            : redirect()->route('comunidad.show', [$team->slug, $comunidad])->with('status', 'Comunidad creada.');
    }

    public function show(Request $request, string $current_team, Comunidad $comunidad): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$comunidad, $team]);

        $comunidad->loadCount('miembros')->load([
            'miembros' => fn ($query) => $query->where('users.id', '!=', $comunidad->creador_id),
            'solicitudes' => fn ($query) => $query->where('estado', 'pendiente')->with('usuario'),
        ]);

        return $request->expectsJson()
            ? response()->json($comunidad)
            : response()->view('gamer.show', [
                'resource' => 'comunidad',
                'title' => $comunidad->nombre,
                'item' => $comunidad,
                'team' => $team,
            ]);
    }

    public function edit(Request $request, string $current_team, Comunidad $comunidad): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$comunidad, $team]);

        return $request->expectsJson()
            ? response()->json($comunidad)
            : response()->view('gamer.form', [
                'resource' => 'comunidad',
                'title' => 'Editar comunidad',
                'item' => $comunidad,
                'team' => $team,
            ]);
    }

    public function update(Request $request, string $current_team, Comunidad $comunidad): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$comunidad, $team]);

        $validated = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'imagen' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:4096'],
            'imagen_url' => ['nullable', 'url', 'max:2048'],
            'juego_principal' => ['nullable', 'string', 'max:100'],
            'plataforma' => ['nullable', 'string', 'max:50'],
            'region' => ['nullable', 'string', 'max:50'],
            'idioma' => ['nullable', 'string', 'max:50'],
            'modalidad' => ['nullable', 'string', 'max:50'],
            'horario' => ['nullable', 'string', 'max:50'],
            'tipo' => ['nullable', 'string', 'max:50'],
            'nivel' => ['nullable', 'string', 'max:50'],
            'rango' => ['nullable', 'string', 'max:50'],
            'estado' => ['nullable', 'string', 'max:50'],
            'max_miembros' => ['nullable', 'integer', 'min:2', 'max:500'],
        ]);

        if ($request->hasFile('imagen')) {
            if ($comunidad->imagen_url && ! filter_var($comunidad->imagen_url, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($comunidad->imagen_url);
            }
            $validated['imagen_url'] = $request->file('imagen')->store('comunidades', 'public');
        }

        unset($validated['imagen']);

        $comunidad->update($validated);

        return $request->expectsJson()
            ? response()->json($comunidad->fresh())
            : redirect()->route('comunidad.show', [$team->slug, $comunidad])->with('status', 'Comunidad actualizada.');
    }

    public function updateMemberRole(Request $request, string $current_team, Comunidad $comunidad, User $user): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$comunidad, $team]);

        $validated = $request->validate([
            'rol' => ['required', 'string', 'in:admin,moderador,miembro'],
        ]);

        $comunidad->miembros()->syncWithoutDetaching([
            $user->id => ['rol' => $validated['rol']],
        ]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Rol actualizado.', 'rol' => $validated['rol']])
            : redirect()->route('comunidad.show', [$team->slug, $comunidad])->with('status', 'Rol actualizado.');
    }

    public function inviteMember(Request $request, string $current_team, Comunidad $comunidad): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$comunidad, $team]);

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = User::query()->whereKey($validated['user_id'])->firstOrFail();

        if (! $user->belongsToTeam($team)) {
            abort(422, 'Este usuario no pertenece al equipo.');
        }

        $comunidad->miembros()->syncWithoutDetaching([
            $user->id => ['rol' => 'miembro'],
        ]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Miembro invitado.', 'user_id' => $user->id])
            : redirect()->route('comunidad.show', [$team->slug, $comunidad])->with('status', 'Miembro invitado a la comunidad.');
    }

    public function transferOwnership(Request $request, string $current_team, Comunidad $comunidad): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$comunidad, $team]);

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $newOwner = User::query()->whereKey($validated['user_id'])->firstOrFail();

        abort_unless($newOwner->belongsToTeam($team), 422, 'El nuevo administrador debe pertenecer al equipo.');
        abort_unless($comunidad->miembros()->whereKey($newOwner->id)->exists(), 422, 'El nuevo administrador debe ser miembro de la comunidad.');

        DB::transaction(function () use ($comunidad, $newOwner): void {
            $oldOwnerId = $comunidad->creador_id;
            $comunidad->update(['creador_id' => $newOwner->id]);
            $comunidad->miembros()->syncWithoutDetaching([
                $newOwner->id => ['rol' => 'admin'],
            ]);

            if ($oldOwnerId && $oldOwnerId !== $newOwner->id) {
                $comunidad->miembros()->updateExistingPivot($oldOwnerId, ['rol' => 'miembro']);
            }
        });

        return $request->expectsJson()
            ? response()->json(['message' => 'Administración transferida.', 'user_id' => $newOwner->id])
            : back()->with('status', 'La administración de la comunidad fue transferida.');
    }

    public function acceptRequest(Request $request, string $current_team, Comunidad $comunidad, ComunidadSolicitud $solicitud): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$comunidad, $team]);
        abort_unless($solicitud->comunidad_id === $comunidad->id && $solicitud->estado === 'pendiente', 404);

        if ($comunidad->max_miembros && $comunidad->miembros()->count() >= $comunidad->max_miembros) {
            abort(422, 'La comunidad ya alcanzó su capacidad máxima.');
        }

        DB::transaction(function () use ($comunidad, $solicitud, $team): void {
            $team->members()->syncWithoutDetaching([
                $solicitud->user_id => ['role' => 'member'],
            ]);
            $comunidad->miembros()->syncWithoutDetaching([
                $solicitud->user_id => ['rol' => 'miembro'],
            ]);
            $solicitud->update(['estado' => 'aceptada']);
        });

        return $request->expectsJson()
            ? response()->json(['message' => 'Solicitud aceptada.'])
            : back()->with('status', 'Solicitud aceptada y usuario agregado a la comunidad.');
    }

    public function rejectRequest(Request $request, string $current_team, Comunidad $comunidad, ComunidadSolicitud $solicitud): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('update', [$comunidad, $team]);
        abort_unless($solicitud->comunidad_id === $comunidad->id && $solicitud->estado === 'pendiente', 404);

        $solicitud->update(['estado' => 'rechazada']);

        return $request->expectsJson()
            ? response()->json(['message' => 'Solicitud rechazada.'])
            : back()->with('status', 'Solicitud rechazada.');
    }

    public function destroy(Request $request, string $current_team, Comunidad $comunidad): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('delete', [$comunidad, $team]);

        $comunidad->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Comunidad archivada correctamente.'])
            : redirect()->route('comunidad.index', $team->slug)->with('status', 'Comunidad archivada.');
    }
}
