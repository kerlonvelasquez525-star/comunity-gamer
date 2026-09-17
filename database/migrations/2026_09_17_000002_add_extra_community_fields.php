<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comunidades', function (Blueprint $table): void {
            if (! Schema::hasColumn('comunidades', 'tipo')) {
                $table->string('tipo')->nullable()->after('descripcion');
            }

            if (! Schema::hasColumn('comunidades', 'nivel')) {
                $table->string('nivel')->nullable()->after('tipo');
            }

            if (! Schema::hasColumn('comunidades', 'rango')) {
                $table->string('rango')->nullable()->after('nivel');
            }

            if (! Schema::hasColumn('comunidades', 'estado')) {
                $table->string('estado')->nullable()->after('rango');
            }

            if (! Schema::hasColumn('comunidades', 'max_miembros')) {
                $table->unsignedInteger('max_miembros')->nullable()->after('estado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('comunidades', function (Blueprint $table): void {
            $table->dropColumn(['tipo', 'nivel', 'rango', 'estado', 'max_miembros']);
        });
    }
};
