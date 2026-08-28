<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amistades', function (Blueprint $table) {
            $table->id();
            // Aquí sí aplica cascade, porque si el usuario no existe, la amistad tampoco tiene sentido
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('amigo_id')->constrained('users')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'aceptada', 'bloqueada'])->default('pendiente');
            $table->timestamps();

            $table->unique(['user_id', 'amigo_id']);
            $table->index(['user_id', 'estado']); // Índice compuesto para rendimiento
            $table->index(['amigo_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amistades');
    }
};
