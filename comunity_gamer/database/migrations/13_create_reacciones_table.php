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

        Schema::create('reacciones', function (Blueprint $table) {
            $table->integer('id_reaccion')->primary()->autoIncrement();
            $table->integer('id_usuario');
            $table->enum('tipo', ["like","love","haha","wow","sad","angry"]);
            $table->integer('id_publicacion')->nullable();
            $table->integer('id_mensaje')->nullable();
            $table->integer('id_comentario')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reacciones');
    }
};
