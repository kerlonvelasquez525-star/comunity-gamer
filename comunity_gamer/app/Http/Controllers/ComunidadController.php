<?php

namespace App\Http\Controllers;

use App\Models\Comunidad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComunidadController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $comunidades = Comunidad::query()
            ->when($request->filled('buscar'), fn ($query) => $query->where('nombre', 'like', '%'.$request->string('buscar').'%'))
            ->withCount('miembros')
            ->latest()
            ->paginate($request->integer('per_page', 12));

        return response()->json($comunidades);
    }
    public function create(): JsonResponse
    {
        return response()->json(['message' => 'Formulario de creación disponible.']);
    }
    public function store(Request $request): JsonResponse
    {
        $comunidad = Comunidad::create($request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
        ]) + ['creador_id' => $request->user()->id]);

        return response()->json($comunidad, 201);
    }
    public function show(Comunidad $comunidad): JsonResponse
    {
        return response()->json($comunidad->loadCount('miembros'));
    }
    public function edit(Comunidad $comunidad): JsonResponse
    {
        return response()->json($comunidad);
    }
    public function update(Request $request, Comunidad $comunidad): JsonResponse
    {
        $comunidad->update($request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
        ]));

        return response()->json($comunidad->fresh());
    }

    public function destroy(Comunidad $comunidad): JsonResponse
    {
        $comunidad->delete();

        return response()->json(['message' => 'Comunidad archivada correctamente.']);
    }
}
