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

        Schema::create('mensajes_privados', function (Blueprint $table) {
            $table->integer('id_mensaje')->primary()->autoIncrement();
            $table->integer('id_emisor');
            $table->foreign('id_emisor')->references('id_usuario')->on('usuarios');
            $table->integer('id_receptor');
            $table->foreign('id_receptor')->references('id_usuario')->on('usuarios');
            $table->text('contenido');
            $table->timestamp('fecha_envio')->useCurrent();
            $table->boolean('leido');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajes_privados');
    }
};
