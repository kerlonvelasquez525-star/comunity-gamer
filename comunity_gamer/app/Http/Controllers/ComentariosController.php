<?php

namespace App\Http\Controllers;

use App\Models\Comentarios;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComentariosController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $comentarios = Comentarios::query()
            ->when($request->filled('publicacion_id'), fn ($query) => $query->where('id_publicacion', $request->integer('publicacion_id')))
            ->latest('fecha_comentario')
            ->paginate($request->integer('per_page', 20));

        return response()->json($comentarios);
    }

    public function create(): JsonResponse
    {
        return response()->json(['message' => 'Formulario de creación disponible.']);
    }

    public function store(Request $request): JsonResponse
    {
        $comentario = Comentarios::create($request->validate([
            'id_publicacion' => ['required', 'integer', 'exists:publicaciones,id'],
            'id_usuario' => ['required', 'integer', 'exists:users,id'],
            'contenido' => ['required', 'string', 'max:2000'],
        ]));

        return response()->json($comentario, 201);
    }

    public function show(Comentarios $comentario): JsonResponse
    {
        return response()->json($comentario);
    }

    public function edit(Comentarios $comentario): JsonResponse
    {
        return response()->json($comentario);
    }

    public function update(Request $request, Comentarios $comentario): JsonResponse
    {
        $comentario->update($request->validate([
            'contenido' => ['sometimes', 'required', 'string', 'max:2000'],
        ]));

        return response()->json($comentario->fresh());
    }

    public function destroy(Comentarios $comentario): JsonResponse
    {
        $comentario->delete();

        return response()->json(['message' => 'Comentario eliminado correctamente.']);
    }
}
