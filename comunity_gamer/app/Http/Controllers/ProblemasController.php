<?php

namespace App\Http\Controllers;

use App\Models\Problemas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProblemasController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $problemas = Problemas::query()
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')))
            ->when($request->filled('prioridad'), fn ($query) => $query->where('prioridad', $request->string('prioridad')))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json($problemas);
    }
   public function create(): JsonResponse
    {
        return response()->json(['message' => 'Formulario de reporte disponible.']);
    }

    public function store(Request $request): JsonResponse
    {
        $problema = Problemas::create($request->validate([
            'titulo' => ['required', 'string', 'max:160'],
            'descripcion' => ['required', 'string', 'max:5000'],
            'prioridad' => ['required', 'in:baja,media,alta,critica'],
            'plataforma' => ['nullable', 'string', 'max:80'],
        ]) + [
            'estado' => 'abierto',
            'user_id' => $request->user()->id,
        ]);

        return response()->json($problema, 201);
    }
    public function show(Problemas $problema): JsonResponse
    {
        return response()->json($problema);
    }
    public function edit(Problemas $problema): JsonResponse
    {
        return response()->json($problema);
    }
    public function update(Request $request, Problemas $problema): JsonResponse
    {
        $problema->update($request->validate([
            'titulo' => ['sometimes', 'required', 'string', 'max:160'],
            'descripcion' => ['sometimes', 'required', 'string', 'max:5000'],
            'prioridad' => ['sometimes', 'required', 'in:baja,media,alta,critica'],
            'estado' => ['sometimes', 'required', 'in:abierto,en_progreso,resuelto,cerrado'],
            'plataforma' => ['nullable', 'string', 'max:80'],
        ]));

        return response()->json($problema->fresh());
    }
    public function destroy(Problemas $problema): JsonResponse
    {
        $problema->delete();

        return response()->json(['message' => 'Reporte eliminado correctamente.']);
    }
}
