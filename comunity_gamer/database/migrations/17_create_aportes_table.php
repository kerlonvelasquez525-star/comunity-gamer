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

        Schema::create('aportes', function (Blueprint $table) {
            $table->integer('id_aporte')->primary()->autoIncrement();
            $table->integer('id_hilo');
            $table->foreign('id_hilo')->references('id_hilo')->on('hilos');
            $table->integer('id_usuario');
            $table->text('contenido');
            $table->timestamp('fecha')->useCurrent();
            $table->integer('id_aporte_padre')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aportes');
    }
};
