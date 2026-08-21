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
        Schema::disableForeignKeyConstraints();

        Schema::create('respuestas_soporte', function (Blueprint $table) {
            $table->integer('id_respuesta')->primary()->autoIncrement();
            $table->integer('id_reporte');
            $table->foreign('id_reporte')->references('id_reporte')->on('reportes_soporte');
            $table->integer('id_usuario');
            $table->text('contenido');
            $table->boolean('es_solucion');
            $table->timestamp('fecha')->useCurrent();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuestas_soporte');
    }
};
