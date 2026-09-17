<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('noticias_comentarios', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('noticia_id')->constrained('noticias')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('contenido');
            $table->timestamps();
            $table->index(['noticia_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noticias_comentarios');
    }
};
