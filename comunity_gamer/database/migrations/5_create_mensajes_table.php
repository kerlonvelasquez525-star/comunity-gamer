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

        Schema::create('mensajes', function (Blueprint $table) {
            $table->integer('id_mensaje')->primary()->autoIncrement();
            $table->integer('id_canal');
            $table->foreign('id_canal')->references('id_canal')->on('canales');
            $table->integer('id_usuario');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios');
            $table->text('contenido');
            $table->timestamp('fecha_envio')->useCurrent();
            $table->integer('id_mensaje_respuesta')->nullable();
            $table->foreign('id_mensaje_respuesta')->references('id_mensaje')->on('mensajes');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};
