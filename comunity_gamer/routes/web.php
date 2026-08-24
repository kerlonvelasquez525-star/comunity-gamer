<?php

use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\ComentariosController;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\ProblemasController;

Route::view('/', 'welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
        
        // Tus rutas de recursos:
        Route::resource('home', HomeController::class);
        Route::resource('noticias', NoticiasController::class);
        Route::resource('comentarios', ComentariosController::class);
        Route::resource('comunidad', ComunidadController::class);
        Route::resource('problemas', ProblemasController::class);
    });

require __DIR__.'/settings.php';