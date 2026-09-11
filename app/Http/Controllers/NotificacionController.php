<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificacionController extends Controller
{
    public function index(Request $request): Response
    {
        $items = Notificacion::query()
            ->where('user_id', $request->user()->id)
            ->latest('created_at')
            ->paginate($request->integer('per_page', 12))
            ->appends($request->query());

        return $request->expectsJson()
            ? response()->json($items)
            : response()->view('gamer.index', [
                'resource' => 'notificaciones',
                'title' => 'Notificaciones',
                'items' => $items,
                'team' => (object) ['slug' => null, 'name' => 'Mi perfil'],
            ]);
    }

    public function markAsRead(Request $request, Notificacion $notificacion): Response
    {
        if ($notificacion->user_id !== $request->user()->id) {
            abort(403);
        }

        $notificacion->update(['leida_en' => now()]);

        return $request->expectsJson()
            ? response()->json($notificacion->fresh())
            : back()->with('status', 'Notificación marcada como leída.');
    }

    public function markAllAsRead(Request $request): Response
    {
        Notificacion::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('leida_en')
            ->update(['leida_en' => now()]);

        return $request->expectsJson()
            ? response()->json(['message' => 'Notificaciones marcadas como leídas.'])
            : back()->with('status', 'Notificaciones marcadas como leídas.');
    }

    public function destroy(Request $request, Notificacion $notificacion): Response
    {
        if ($notificacion->user_id !== $request->user()->id) {
            abort(403);
        }

        $notificacion->delete();

        return $request->expectsJson()
            ? response()->json(['message' => 'Notificación eliminada.'])
            : back()->with('status', 'Notificación eliminada.');
    }
}
