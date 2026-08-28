<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            // Evita borrar el contenido valioso si el usuario se da de baja
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('titulo')->nullable();
            $table->text('contenido');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publicaciones');
    }
};
