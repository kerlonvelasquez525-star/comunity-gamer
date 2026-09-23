<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunidad_solicitudes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('comunidad_id')->constrained('comunidades')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('estado')->default('pendiente');
            $table->timestamps();

            $table->unique(['comunidad_id', 'user_id']);
            $table->index(['comunidad_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunidad_solicitudes');
    }
};
