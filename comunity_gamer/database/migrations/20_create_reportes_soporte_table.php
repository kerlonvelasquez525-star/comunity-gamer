<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_soporte', function (Blueprint $table) {
            $table->id('id_reporte');
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->string('asunto', 150);
            $table->text('descripcion');
            $table->enum('estado', ['abierto', 'en_proceso', 'cerrado'])->default('abierto');
            $table->timestamp('fecha_creacion')->useCurrent();

            $table->index(['estado', 'fecha_creacion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_soporte');
    }
};
