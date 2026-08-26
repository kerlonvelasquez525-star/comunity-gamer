<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reacciones', function (Blueprint $table) {
            $table->id('id_reaccion');
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_publicacion')->constrained('publicaciones')->cascadeOnDelete();
            $table->string('tipo', 20);
            $table->timestamp('fecha')->useCurrent();

            $table->unique(['id_usuario', 'id_publicacion']);
            $table->index(['id_publicacion', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reacciones');
    }
};
