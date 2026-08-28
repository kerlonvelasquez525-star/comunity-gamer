<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('comentarios')) {
            return;
        }

        Schema::create('comentarios', function (Blueprint $table) {
            $table->id('id_comentario');
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('id_publicacion')->constrained('publicaciones')->cascadeOnDelete();
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->text('contenido');
            $table->timestamp('fecha_comentario')->useCurrent();
            $table->timestamps();

            $table->index(['id_publicacion', 'fecha_comentario']);
            $table->index('id_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
