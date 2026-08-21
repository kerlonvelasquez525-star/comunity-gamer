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
        Schema::disableForeignKeyConstraints();

        Schema::create('comentarios', function (Blueprint $table) {
            $table->integer('id_comentario')->primary()->autoIncrement();
            $table->integer('id_publicacion');
            $table->foreign('id_publicacion')->references('id_publicacion')->on('publicaciones');
            $table->integer('id_usuario');
            $table->text('contenido');
            $table->timestamp('fecha')->useCurrent();
            $table->integer('id_comentario_padre')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
