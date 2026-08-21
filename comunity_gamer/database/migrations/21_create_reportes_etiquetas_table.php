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

        Schema::create('reportes_etiquetas', function (Blueprint $table) {
            $table->integer('id_reporte')->primary();
            $table->foreign('id_reporte')->references('id_reporte')->on('reportes_soporte');
            $table->integer('id_etiqueta')->primary();
            $table->foreign('id_etiqueta')->references('id_etiqueta')->on('etiquetas');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes_etiquetas');
    }
};
