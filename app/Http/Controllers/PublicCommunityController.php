<?php

namespace App\Http\Controllers;

use App\Models\Comunidad;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicCommunityController extends Controller
{
    public function index(Request $request): View
    {
        $comunidades = Comunidad::query()
            ->with('creador')
            ->withCount('miembros')
            ->latest()
            ->limit(12)
            ->get();

        return view('public-community', [
            'items' => $comunidades,
            'title' => 'Comunidades activas',
        ]);
    }
}
