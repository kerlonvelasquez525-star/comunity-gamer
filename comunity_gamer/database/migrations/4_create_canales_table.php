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

        Schema::create('canales', function (Blueprint $table) {
            $table->integer('id_canal')->primary()->autoIncrement();
            $table->integer('id_comunidad');
            $table->foreign('id_comunidad')->references('id_comunidad')->on('comunidades');
            $table->string('nombre', 100);
            $table->enum('tipo', ["texto","voz"]);
            $table->timestamp('fecha_creacion')->useCurrent();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canales');
    }
};
