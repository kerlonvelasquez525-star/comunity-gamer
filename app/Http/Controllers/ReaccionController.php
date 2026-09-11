<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use App\Models\Reaccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ReaccionController extends Controller
{
    public function toggle(Request $request, string $current_team, Publicacion $publicacion): Response
    {
        $team = $this->currentTeam($request);
        Gate::authorize('view', [$publicacion, $team]);
        $tipo = $request->string('tipo', 'like')->toString();
        $reaction = Reaccion::query()->where('id_usuario', $request->user()->id)->where('id_publicacion', $publicacion->id)->first();

        if ($reaction?->tipo === $tipo) {
            $reaction->delete();

            return $request->expectsJson() ? response()->json(['active' => false]) : back();
        }

        $reaction ??= new Reaccion(['id_usuario' => $request->user()->id, 'id_publicacion' => $publicacion->id]);
        $reaction->fill(['tipo' => $tipo, 'fecha' => now()])->save();

        return $request->expectsJson() ? response()->json(['active' => true, 'tipo' => $tipo]) : back();
    }
}
