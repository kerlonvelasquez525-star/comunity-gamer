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

        Schema::create('foros', function (Blueprint $table) {
            $table->integer('id_foro')->primary()->autoIncrement();
            $table->integer('id_juego');
            $table->foreign('id_juego')->references('id_juego')->on('juegos');
            $table->integer('id_idioma');
            $table->foreign('id_idioma')->references('id_idioma')->on('idiomas');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->unique(['id_juego', 'id_idioma']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foros');
    }
};
