<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ComentariosController;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\ProblemasController;
use App\Http\Controllers\PublicNewsController;
use App\Http\Middleware\EnsureTeamMembership;
use App\Http\Middleware\SetTeamUrlDefaults;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
| 'home' es la landing pública (nexus_community).
| Aquí se exponen las páginas accesibles sin necesidad de pertenecer a un equipo.
*/

// Página principal pública que muestra noticias oficiales y la comunidad.
Route::get('/', [PublicNewsController::class, 'index'])->name('home');

// Permite comentar noticias oficiales desde la vista pública, solo para usuarios autenticados.

Route::post('noticias-oficiales/{noticia}/comentarios', [PublicNewsController::class, 'comment'])
    ->middleware(['auth', 'verified'])
    ->name('official-news.comments.store');

Route::middleware(['auth', 'verified'])->group(function (): void {
    // Chat interno entre usuarios autenticados y verificados.
    Route::get('chat/{recipient}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('chat/{recipient}', [ChatController::class, 'store'])->name('chat.store');
});

/*
|--------------------------------------------------------------------------
| Rutas de configuración
|--------------------------------------------------------------------------
| Se registran ANTES del grupo con prefijo {current_team}.
*/

require __DIR__.'/settings.php';

/*
|--------------------------------------------------------------------------
| Rutas por equipo
|--------------------------------------------------------------------------
*/

// Rutas específicas del equipo.
// Se requieren sesión autenticada, verificación de email y pertenencia al equipo actual.
Route::prefix('{current_team}')
    ->middleware([
        'auth',
        'verified',
        SetTeamUrlDefaults::class,
        EnsureTeamMembership::class,
    ])
    ->group(function (): void {
        // Panel principal del equipo con resumen de noticias, comunidades y soporte.
        Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');

        // CRUD de noticias del equipo: listar, crear, editar, ver y eliminar.
        Route::resource('noticias', NoticiasController::class);

        // CRUD de comentarios del equipo y su contenido asociado.
        Route::resource('comentarios', ComentariosController::class);

        // Gestión de comunidades del equipo.
        Route::resource('comunidad', ComunidadController::class);

        // Gestión de problemas o incidencias del equipo.
        Route::resource('problemas', ProblemasController::class);
    });
