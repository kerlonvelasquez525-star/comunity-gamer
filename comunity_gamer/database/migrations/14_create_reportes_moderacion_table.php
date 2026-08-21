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

        Schema::create('reportes_moderacion', function (Blueprint $table) {
            $table->integer('id_reporte')->primary()->autoIncrement();
            $table->integer('id_usuario_reporta');
            $table->integer('id_usuario_reportado')->nullable();
            $table->integer('id_mensaje')->nullable();
            $table->integer('id_publicacion')->nullable();
            $table->string('motivo', 255);
            $table->enum('estado', ["pendiente","revisado","descartado"]);
            $table->timestamp('fecha')->useCurrent();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes_moderacion');
    }
};
