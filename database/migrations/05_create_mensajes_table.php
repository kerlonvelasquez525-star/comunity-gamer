<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID para evitar cuellos de botella
            $table->foreignId('canal_id')->constrained('canales', 'id_canal')->onDelete('cascade');
            // Si el usuario se elimina, el mensaje queda con user_id NULL ("Usuario eliminado")
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('contenido');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['canal_id', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};
