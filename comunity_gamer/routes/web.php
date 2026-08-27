<?php

use App\Http\Controllers\ComentariosController;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\ProblemasController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::view('/', 'nexus_community')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');

        // Tus rutas de recursos:
        Route::get('home', [HomeController::class, 'index'])->name('home.index');
        Route::resource('noticias', NoticiasController::class);
        Route::resource('comentarios', ComentariosController::class);
        Route::resource('comunidad', ComunidadController::class);
        Route::resource('problemas', ProblemasController::class);
    });

require __DIR__.'/settings.php';


// Route::middleware(['auth', 'verified'])->group(function () {
//     // Ruta de configuración de seguridad adaptada al sistema multi-equipo
//     Route::get('/teams/{current_team}/settings/security', [App\Http\Controllers\Settings\SecurityController::class, 'edit'])
//         ->name('security.edit');
// });