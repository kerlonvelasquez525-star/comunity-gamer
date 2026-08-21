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

        Schema::create('amistades', function (Blueprint $table) {
            $table->integer('id')->primary()->autoIncrement();
            $table->integer('id_usuario1');
            $table->foreign('id_usuario1')->references('id_usuario')->on('usuarios');
            $table->integer('id_usuario2');
            $table->foreign('id_usuario2')->references('id_usuario')->on('usuarios');
            $table->enum('estado', ["pendiente","aceptada","bloqueada"]);
            $table->timestamp('fecha')->useCurrent();
            $table->unique(['id_usuario1', 'id_usuario2']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amistades');
    }
};
