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

        Schema::create('miembros_comunidad', function (Blueprint $table) {
            $table->integer('id')->primary()->autoIncrement();
            $table->integer('id_comunidad');
            $table->foreign('id_comunidad')->references('id_comunidad')->on('comunidades');
            $table->integer('id_usuario');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios');
            $table->enum('rol', ["admin","mod","miembro"]);
            $table->timestamp('fecha_ingreso')->useCurrent();
            $table->unique(['id_comunidad', 'id_usuario']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('miembros_comunidad');
    }
};
