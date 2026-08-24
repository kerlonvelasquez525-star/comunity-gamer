<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id('id_comentario');
            $table->foreignId('id_publicacion')->constrained('publicaciones')->cascadeOnDelete();
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->text('contenido');
            $table->timestamp('fecha_comentario')->useCurrent();

            $table->index(['id_publicacion', 'fecha_comentario']);
            $table->index('id_usuario');
        });
    }

    public function down(): void {
        Schema::dropIfExists('comentarios');
    }
};