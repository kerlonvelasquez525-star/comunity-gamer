<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_etiquetas', function (Blueprint $table) {
            $table->id('id_reporte_etiqueta');
            $table->foreignId('id_reporte')->constrained('reportes_soporte', 'id_reporte')->cascadeOnDelete();
            $table->foreignId('id_etiqueta')->constrained('etiquetas', 'id_etiqueta')->cascadeOnDelete();
            $table->unique(['id_reporte', 'id_etiqueta']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_etiquetas');
    }
};
