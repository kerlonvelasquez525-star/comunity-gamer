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
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_usuario'); // Mantiene la funcionalidad de $table->id() pero renombrado a id_usuario
            $table->string('nombre_usuario', 50)->unique();
            $table->string('email', 100)->unique(); // Campo estandarizado de Laravel para auth
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // Equivalente a contrasena_hash en Laravel Auth
            $table->string('avatar_url', 255)->nullable();
            $table->text('biografia')->nullable();
            $table->enum('estado', ["en_linea", "ausente", "desconectado"])->default('desconectado');
            $table->rememberToken();
            $table->timestamps(); // Genera created_at y updated_at
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};