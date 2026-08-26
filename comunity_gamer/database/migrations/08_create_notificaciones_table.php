<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('tipo');
            $table->json('data');
            $table->timestamp('leida_en')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'leida_en']); // Acelera: "Buscar notificaciones no leídas del usuario X"
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
