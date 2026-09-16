<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\noticias;
use App\Models\NoticiasComentario;
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
        return view('nexus_community', [
            'officialNews' => noticias::query()
                ->whereNull('team_id')
                ->where('es_oficial', true)
                ->with(['comentarios' => fn ($query) => $query->with('autor')->latest()])
                ->latest()
                ->limit(6)
                ->get(),
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
