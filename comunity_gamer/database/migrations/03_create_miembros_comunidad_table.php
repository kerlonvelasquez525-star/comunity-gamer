<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('miembros_comunidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comunidad_id')->constrained('comunidades')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('rol')->default('miembro');
            $table->timestamps();

            $table->unique(['comunidad_id', 'user_id']); // Evita duplicados
            $table->index(['user_id', 'rol']); // Acelera: "Buscar todas las comunidades donde el usuario X es admin"
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('miembros_comunidad');
    }
};
