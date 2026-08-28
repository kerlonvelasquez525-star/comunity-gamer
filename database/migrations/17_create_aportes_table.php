<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aportes', function (Blueprint $table) {
            $table->id('id_aporte');
            $table->foreignId('id_hilo')->constrained('hilos', 'id_hilo')->cascadeOnDelete();
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->text('contenido');
            $table->timestamp('fecha_aporte')->useCurrent();

            $table->index(['id_hilo', 'fecha_aporte']);
            $table->index('id_usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aportes');
    }
};
