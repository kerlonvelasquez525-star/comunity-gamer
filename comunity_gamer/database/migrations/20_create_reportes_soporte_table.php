<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reportes_soporte', function (Blueprint $table) {
            $table->integer('id_reporte')->primary()->autoIncrement();
            $table->integer('id_usuario');
            $table->integer('id_juego');
            $table->integer('id_plataforma');
            $table->enum('tipo', ["bug","fix","truco"]);
            $table->enum('estado', ["abierto","confirmado","resuelto"]);
            $table->string('titulo', 200);
            $table->text('contenido');
            $table->timestamp('fecha_publicacion')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes_soporte');
    }
};
