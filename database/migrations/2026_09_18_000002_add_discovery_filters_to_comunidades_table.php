<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comunidades', function (Blueprint $table): void {
            $table->string('juego_principal', 100)->nullable()->after('descripcion');
            $table->string('plataforma', 50)->nullable()->after('juego_principal');
            $table->string('region', 50)->nullable()->after('plataforma');
            $table->string('idioma', 50)->nullable()->after('region');
            $table->string('modalidad', 50)->nullable()->after('idioma');
            $table->string('horario', 50)->nullable()->after('modalidad');

            $table->index(['juego_principal', 'plataforma']);
            $table->index(['region', 'modalidad']);
        });
    }

    public function down(): void
    {
        Schema::table('comunidades', function (Blueprint $table): void {
            $table->dropIndex(['juego_principal', 'plataforma']);
            $table->dropIndex(['region', 'modalidad']);
            $table->dropColumn([
                'juego_principal',
                'plataforma',
                'region',
                'idioma',
                'modalidad',
                'horario',
            ]);
        });
    }
};
