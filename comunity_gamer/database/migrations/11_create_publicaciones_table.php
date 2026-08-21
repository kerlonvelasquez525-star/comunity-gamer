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
        Schema::create('publicaciones', function (Blueprint $table) {
            $table->integer('id_publicacion')->primary()->autoIncrement();
            $table->integer('id_usuario');
            $table->integer('id_comunidad')->nullable();
            $table->text('contenido')->nullable();
            $table->string('multimedia_url', 255)->nullable();
            $table->timestamp('fecha_publicacion')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicaciones');
    }
};
