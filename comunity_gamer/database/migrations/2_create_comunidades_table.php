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

        Schema::create('comunidades', function (Blueprint $table) {
            $table->integer('id_comunidad')->primary()->autoIncrement();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->integer('id_creador');
            $table->foreign('id_creador')->references('id_usuario')->on('usuarios');
            $table->enum('tipo', ["publica","privada"]);
            $table->timestamp('fecha_creacion')->useCurrent();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comunidades');
    }
};
