<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hilos', function (Blueprint $table) {
            $table->id('id_hilo');
            $table->foreignId('id_foro')->constrained('foros', 'id_foro')->cascadeOnDelete();
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->string('titulo', 150);
            $table->text('contenido');
            $table->timestamp('fecha_creacion')->useCurrent();

            $table->index(['id_foro', 'fecha_creacion']);
            $table->index('id_usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hilos');
    }
};
