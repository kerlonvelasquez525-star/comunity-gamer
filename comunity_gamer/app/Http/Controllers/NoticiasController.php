<?php

namespace App\Http\Controllers;

use App\Models\noticias;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoticiasController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $noticias = noticias::query()
            ->latest()
            ->paginate($request->integer('per_page', 12));

        return response()->json($noticias);
    }

    public function create(): JsonResponse
    {
        return response()->json(['message' => 'Formulario de creación disponible.']);
    }
    public function store(Request $request): JsonResponse
    {
        $noticia = noticias::create($request->validate([
            'titulo' => ['required', 'string', 'max:180'],
            'contenido' => ['required', 'string', 'max:10000'],
            'categoria' => ['nullable', 'string', 'max:80'],
            'imagen_url' => ['nullable', 'url', 'max:2048'],
        ]));

        return response()->json($noticia, 201);
    }

    public function show(noticias $noticia): JsonResponse
    {
        return response()->json($noticia);
    }
    public function edit(noticias $noticia): JsonResponse
    {
        return response()->json($noticia);
    }
    public function update(Request $request, noticias $noticia): JsonResponse
    {
        $noticia->update($request->validate([
            'titulo' => ['sometimes', 'required', 'string', 'max:180'],
            'contenido' => ['sometimes', 'required', 'string', 'max:10000'],
            'categoria' => ['nullable', 'string', 'max:80'],
            'imagen_url' => ['nullable', 'url', 'max:2048'],
        ]));

        return response()->json($noticia->fresh());
    }

    public function destroy(noticias $noticia): JsonResponse
    {
        $noticia->delete();

        return response()->json(['message' => 'Noticia eliminada correctamente.']);
    }
}
