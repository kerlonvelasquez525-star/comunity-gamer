<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetTeamUrlDefaults
{
    /**
     * Establece los parámetros URL por defecto para las rutas basadas en equipos.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Obtener el slug si el usuario está autenticado y tiene equipo
        $teamSlug = $request->user()?->currentTeam?->slug;

        // 2. Si no hay usuario o no tiene equipo, asignamos un slug genérico o el parámetro de la ruta activa
        if (! $teamSlug) {
            $teamSlug = $request->route('current_team') ?? $request->route('team') ?? 'default';
        }

        // 3. Registrar los parámetros globales para la generación de URLs con route()
        URL::defaults([
            'current_team' => $teamSlug,
            'team'         => $teamSlug,
        ]);

        return $next($request);
    }
}