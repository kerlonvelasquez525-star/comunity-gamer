<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('canales', function (Blueprint $table) {
            $table->id('id_canal');
            $table->foreignId('comunidad_id')
                ->constrained('comunidades')
                ->cascadeOnDelete();
            $table->string('nombre', 100);
            $table->enum('tipo', ['texto', 'voz']);
            $table->timestamp('fecha_creacion')->useCurrent();

            $table->unique(['comunidad_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('canales');
    }
};
