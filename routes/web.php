<?php

use App\Http\Controllers\ComentariosController;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\ProblemasController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas publicas
|--------------------------------------------------------------------------
| 'home' es la landing publica (nexus_community). Es el nombre que usan
| layouts/auth/*.blade.php, pages/auth/login y pages/auth/register, asi que
| NO puede volver a declararse dentro del grupo de equipos.
*/

Route::view('/', 'nexus_community')->name('home');

/*
|--------------------------------------------------------------------------
| Rutas de configuracion
|--------------------------------------------------------------------------
| Se registran ANTES del grupo con prefijo {current_team}. Si van despues,
| el patron '/{current_team}' captura '/settings' y devuelve 403.
*/

require __DIR__.'/settings.php';

/*
|--------------------------------------------------------------------------
| Rutas por equipo
|--------------------------------------------------------------------------
*/

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function (): void {
        Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');

        Route::resource('noticias', NoticiasController::class);
        Route::resource('comentarios', ComentariosController::class);
        Route::resource('comunidad', ComunidadController::class);
        Route::resource('problemas', ProblemasController::class);
    });
