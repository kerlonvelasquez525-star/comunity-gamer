<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('juego_idioma', function (Blueprint $table) {
            $table->id();
            $table->foreignId('juego_id')->constrained('juegos')->cascadeOnDelete();
            $table->foreignId('idioma_id')->constrained('idiomas')->cascadeOnDelete();
            $table->unique(['juego_id', 'idioma_id']);
        });

        Schema::create('juego_plataforma', function (Blueprint $table) {
            $table->id();
            $table->foreignId('juego_id')->constrained('juegos')->cascadeOnDelete();
            $table->foreignId('plataforma_id')->constrained('plataformas')->cascadeOnDelete();
            $table->unique(['juego_id', 'plataforma_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('juego_plataforma');
        Schema::dropIfExists('juego_idioma');
    }
};
