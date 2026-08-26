<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foros', function (Blueprint $table) {
            $table->id('id_foro');
            $table->foreignId('comunidad_id')->nullable()->constrained('comunidades')->nullOnDelete();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->timestamp('fecha_creacion')->useCurrent();

            $table->unique(['comunidad_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foros');
    }
};
