<?php

use App\Http\Controllers\AmistadController;
use App\Http\Controllers\AporteController;
use App\Http\Controllers\CanalController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ComentariosController;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\EtiquetaController;
use App\Http\Controllers\ForoController;
use App\Http\Controllers\HiloController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IdiomaController;
use App\Http\Controllers\JuegoController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PlataformaController;
use App\Http\Controllers\ProblemasController;
use App\Http\Controllers\PublicacionController;
use App\Http\Controllers\PublicNewsController;
use App\Http\Controllers\ReaccionController;
use App\Http\Controllers\ReporteModeracionController;
use App\Http\Controllers\ReporteSoporteController;
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
    Route::get('chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('chat/bot', [ChatbotController::class, 'answer'])
        ->middleware('throttle:30,1')
        ->name('chat.bot.answer');
    Route::get('chat/{recipient}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('chat/{recipient}', [ChatController::class, 'store'])->name('chat.store');
    Route::get('amistades', [AmistadController::class, 'index'])->name('amistades.index');
    Route::post('amistades', [AmistadController::class, 'store'])->name('amistades.store');
    Route::patch('amistades/{amistad}/accept', [AmistadController::class, 'accept'])->name('amistades.accept');
    Route::patch('amistades/{amistad}/reject', [AmistadController::class, 'reject'])->name('amistades.reject');
    Route::delete('amistades/{amistad}', [AmistadController::class, 'destroy'])->name('amistades.destroy');
    Route::get('notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::patch('notificaciones/{notificacion}/read', [NotificacionController::class, 'markAsRead'])->name('notificaciones.read');
    Route::patch('notificaciones/read-all', [NotificacionController::class, 'markAllAsRead'])->name('notificaciones.read-all');
    Route::delete('notificaciones/{notificacion}', [NotificacionController::class, 'destroy'])->name('notificaciones.destroy');
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
        Route::resource('publicaciones', PublicacionController::class)->parameters(['publicaciones' => 'publicacion']);
        Route::post('publicaciones/{publicacion}/reacciones', [ReaccionController::class, 'toggle'])->name('publicaciones.reacciones.toggle');
        Route::resource('foros', ForoController::class);
        Route::resource('hilos', HiloController::class);
        Route::resource('aportes', AporteController::class);
        Route::resource('canales', CanalController::class);
        Route::get('canales/{canal}/mensajes', [MensajeController::class, 'index'])->name('canales.mensajes.index');
        Route::post('canales/{canal}/mensajes', [MensajeController::class, 'store'])->name('canales.mensajes.store');
        Route::delete('mensajes/{mensaje}', [MensajeController::class, 'destroy'])->name('mensajes.destroy');
        Route::resource('juegos', JuegoController::class);
        Route::resource('plataformas', PlataformaController::class);
        Route::resource('idiomas', IdiomaController::class);
        Route::resource('etiquetas', EtiquetaController::class);
        Route::get('reportes-moderacion', [ReporteModeracionController::class, 'index'])->name('reportes-moderacion.index');
        Route::post('reportes-moderacion', [ReporteModeracionController::class, 'store'])->name('reportes-moderacion.store');
        Route::get('reportes-moderacion/{reporteModeracion}', [ReporteModeracionController::class, 'show'])->name('reportes-moderacion.show');
        Route::patch('reportes-moderacion/{reporteModeracion}/estado', [ReporteModeracionController::class, 'updateEstado'])->name('reportes-moderacion.estado');
        Route::resource('reportes-soporte', ReporteSoporteController::class)->parameters(['reportes-soporte' => 'reporteSoporte']);
        Route::post('reportes-soporte/{reporteSoporte}/responder', [ReporteSoporteController::class, 'responder'])->name('reportes-soporte.responder');
        Route::post('reportes-soporte/{reporteSoporte}/votar', [ReporteSoporteController::class, 'votar'])->name('reportes-soporte.votar');
    });
