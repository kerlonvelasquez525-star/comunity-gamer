<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comunidades', function (Blueprint $table): void {
            if (! Schema::hasColumn('comunidades', 'imagen_url')) {
                $table->string('imagen_url')->nullable()->after('descripcion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('comunidades', function (Blueprint $table): void {
            if (Schema::hasColumn('comunidades', 'imagen_url')) {
                $table->dropColumn('imagen_url');
            }
        });
    }
};
