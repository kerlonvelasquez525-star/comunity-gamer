<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respuestas_soporte', function (Blueprint $table) {
            $table->id('id_respuesta');
            $table->foreignId('id_reporte')->constrained('reportes_soporte', 'id_reporte')->cascadeOnDelete();
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->text('mensaje');
            $table->timestamp('fecha_respuesta')->useCurrent();

            $table->index(['id_reporte', 'fecha_respuesta']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respuestas_soporte');
    }
};
