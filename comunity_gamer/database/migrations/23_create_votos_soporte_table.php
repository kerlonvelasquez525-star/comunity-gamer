<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votos_soporte', function (Blueprint $table) {
            $table->id('id_voto');
            $table->foreignId('id_reporte')->constrained('reportes_soporte', 'id_reporte')->cascadeOnDelete();
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->smallInteger('voto'); // +1 o -1
            $table->unique(['id_reporte', 'id_usuario']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votos_soporte');
    }
};
