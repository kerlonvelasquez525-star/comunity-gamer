<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetTeamUrlDefaults
{
    public function handle(Request $request, Closure $next): Response
    {
        // Slug directo de la ruta, o el equipo actual del usuario si hay sesión
        $team = $request->route('current_team')
            ?? $request->route('team')
            ?? $request->user()?->currentTeam;

        if ($team) {
            // De la ruta llega un string (el slug); del modelo un objeto
            $slug = match (true) {
                is_string($team) => $team,
                $team instanceof UrlRoutable => $team->getRouteKey(),
                default => null,
            };

            if ($slug !== null) {
                URL::defaults([
                    'current_team' => $slug,
                    'team' => $slug,
                ]);
            }
        }

        return $next($request);
    }
}
