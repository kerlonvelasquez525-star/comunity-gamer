<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The canonical communities table is created by 02_create_comunidades_table.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kept as a no-op for compatibility with databases that ran this migration.
    }
};
