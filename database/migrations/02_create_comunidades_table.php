<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            // Si el creador elimina su cuenta, la comunidad sigue existiendo sin dueño visible
            $table->foreignId('creador_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes(); // Eliminación lógica
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunidades');
    }
};
