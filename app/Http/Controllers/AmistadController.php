<?php

namespace App\Http\Controllers;

use App\Models\Amistad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AmistadController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $items = Amistad::query()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('amigo_id', $user->id);
            })
            ->with(['usuario', 'amigo'])
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
            ->latest()
            ->paginate($request->integer('per_page', 12))
            ->appends($request->query());

        return $request->expectsJson()
            ? response()->json($items)
            : response()->view('gamer.index', [
                'resource' => 'amistades',
                'title' => 'Amistades',
                'items' => $items,
                'team' => (object) ['slug' => null, 'name' => 'Mi perfil'],
            ]);
    }

    public function store(Request $request): Response
    {
        $validated = $request->validate([
            'amigo_id' => ['required', 'integer', 'exists:users,id', 'different:'.$request->user()->id],
        ]);

        $existing = Amistad::query()
            ->where(function ($query) use ($request, $validated) {
                $query->where('user_id', $request->user()->id)
                    ->where('amigo_id', $validated['amigo_id']);
            })
            ->orWhere(function ($query) use ($request, $validated) {
                $query->where('user_id', $validated['amigo_id'])
                    ->where('amigo_id', $request->user()->id);
            })
            ->first();

        if ($existing) {
            return $request->expectsJson()
                ? response()->json($existing, 200)
                : back()->with('status', 'La solicitud de amistad ya existe.');
        }

        $item = Amistad::create([
            'user_id' => $request->user()->id,
            'amigo_id' => $validated['amigo_id'],
            'estado' => 'pendiente',
        ]);

        return $request->expectsJson()
            ? response()->json($item->load(['usuario', 'amigo']), 201)
            : back()->with('status', 'Solicitud enviada.');
    }

    public function accept(Request $request, Amistad $amistad): Response
    {
        Gate::authorize('update', $amistad);

        $amistad->update(['estado' => 'aceptada']);

        return $request->expectsJson()
            ? response()->json($amistad->fresh()->load(['usuario', 'amigo']))
            : back()->with('status', 'Solicitud aceptada.');
    }

    public function reject(Request $request, Amistad $amistad): Response
    {
        Gate::authorize('update', $amistad);

        $amistad->update(['estado' => 'bloqueada']);

        return $request->expectsJson()
            ? response()->json($amistad->fresh())
            : back()->with('status', 'Solicitud rechazada.');
    }

    public function destroy(Request $request, Amistad $amistad): Response
    {
        Gate::authorize('delete', $amistad);

        $amistad->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Amistad eliminada.'])
            : back()->with('status', 'Amistad eliminada.');
    }
}
