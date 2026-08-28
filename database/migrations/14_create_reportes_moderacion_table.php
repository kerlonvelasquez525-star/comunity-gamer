<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_moderacion', function (Blueprint $table) {
            $table->id('id_reporte');
            $table->foreignId('id_reportador')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_reportado')->nullable()->constrained('users')->nullOnDelete();
            $table->text('motivo');
            $table->enum('estado', ['pendiente', 'revisado', 'rechazado'])->default('pendiente');
            $table->timestamp('fecha')->useCurrent();

            $table->index(['estado', 'fecha']);
            $table->index('id_reportado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_moderacion');
    }
};
