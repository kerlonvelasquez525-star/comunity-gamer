<?php

namespace App\Http\Controllers;

use App\Models\noticias;
use App\Models\NoticiasComentario;
use App\Models\Publicacion;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador público para la landing principal de noticias.
 *
 * Muestra las noticias oficiales sin asignar un equipo y permite comentar
 * contenido de origen externo cuando el usuario está autenticado.
 */
class PublicNewsController extends Controller
{
    /**
     * Carga la vista pública con las últimas noticias oficiales del sistema.
     */
    public function index(): View
    {
        $usuariosActivos = User::query()->count();
        $postsDiarios = Publicacion::query()->count() + noticias::query()
            ->whereNull('team_id')
            ->where('es_oficial', true)
            ->count();
        $telemetria = $usuariosActivos > 0
            ? round((User::query()->whereNotNull('email_verified_at')->count() / $usuariosActivos) * 100, 1)
            : 0;

        return view('nexus_community', [
            'officialNews' => noticias::query()
                ->whereNull('team_id')
                ->where('es_oficial', true)
                ->with(['comentarios' => fn ($query) => $query->with('autor')->latest()])
                ->latest()
                ->limit(6)
                ->get(),
            'usuarios_activos' => $usuariosActivos,
            'post_diarios' => $postsDiarios,
            'telemetria' => $telemetria,
        ]);
    }

    public function refresh(): JsonResponse
    {
        $news = noticias::query()
            ->whereNull('team_id')
            ->where('es_oficial', true)
            ->with(['comentarios' => fn ($query) => $query->with('autor')->latest()->limit(3)])
            ->latest()
            ->limit(6)
            ->get();

        return response()->json($news->map(function (noticias $noticia): array {
            return [
                'id' => $noticia->id,
                'comments_count' => $noticia->comentarios->count(),
                'comments' => $noticia->comentarios->map(function (NoticiasComentario $comentario): array {
                    return [
                        'author' => $comentario->autor?->name ?? 'Usuario',
                        'content' => $comentario->contenido,
                    ];
                })->values()->all(),
            ];
        })->values()->all());
    }

    /**
     * Guarda un comentario asociado a una noticia oficial pública.
     */
    public function comment(Request $request, noticias $noticia): RedirectResponse
    {
        abort_unless($noticia->team_id === null && $noticia->es_oficial, 404);

        $validated = $request->validate([
            'contenido' => ['required', 'string', 'max:2000'],
        ]);

        NoticiasComentario::create([
            'noticia_id' => $noticia->id,
            'user_id' => $request->user()->id,
            'contenido' => $validated['contenido'],
        ]);

        return back()->with('status', 'Comentario publicado.');
    }
}
