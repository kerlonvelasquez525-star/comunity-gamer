<?php

namespace App\Http\Controllers;

use App\Models\Comentarios;
use App\Models\Comunidad;
use App\Models\noticias;
use App\Models\Problemas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'noticias_recientes' => noticias::query()->latest()->limit(5)->get(),
            'comunidades_activas' => Comunidad::query()->latest()->limit(5)->get(),
            'problemas_abiertos' => Problemas::query()->whereIn('estado', ['abierto', 'en_progreso'])->count(),
            'comentarios_totales' => Comentarios::query()->count(),
        ]);
    }
    public function create(): JsonResponse
    {
        return response()->json(['message' => 'La portada es de solo lectura.'], 405);
    }
    public function store(Request $request): JsonResponse
    {
        return response()->json(['message' => 'La portada es de solo lectura.'], 405);
    }
    public function show(): JsonResponse
    {
        return $this->index();
    }
    public function edit(): JsonResponse
    {
        return response()->json(['message' => 'La portada es de solo lectura.'], 405);
    }
    public function update(Request $request): JsonResponse
    {
        return response()->json(['message' => 'La portada es de solo lectura.'], 405);
    }
    public function destroy(): JsonResponse
    {
        return response()->json(['message' => 'La portada es de solo lectura.'], 405);
    }
}
