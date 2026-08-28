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
        Schema::create('mensajes_privados', function (Blueprint $table) {
            $table->id('id_mensaje');
            $table->foreignId('id_emisor')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_receptor')->constrained('users')->cascadeOnDelete();
            $table->text('contenido');
            $table->timestamp('fecha_envio')->useCurrent();
            $table->boolean('leido');

            $table->index(['id_receptor', 'leido', 'fecha_envio']);
            $table->index(['id_emisor', 'id_receptor', 'fecha_envio']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajes_privados');
    }
};
