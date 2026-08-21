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

        Schema::create('votos_soporte', function (Blueprint $table) {
            $table->integer('id_voto')->primary()->autoIncrement();
            $table->integer('id_usuario');
            $table->tinyInteger('valor');
            $table->integer('id_reporte')->nullable();
            $table->integer('id_respuesta')->nullable();
            $table->unique(['id_usuario', 'id_respuesta']);
            $table->unique(['id_usuario', 'id_reporte']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votos_soporte');
    }
};
