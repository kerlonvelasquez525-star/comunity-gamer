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

        Schema::create('hilos', function (Blueprint $table) {
            $table->integer('id_hilo')->primary()->autoIncrement();
            $table->integer('id_foro');
            $table->foreign('id_foro')->references('id_foro')->on('foros');
            $table->integer('id_usuario');
            $table->string('titulo', 200);
            $table->text('contenido')->nullable();
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->boolean('fijado');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hilos');
    }
};
