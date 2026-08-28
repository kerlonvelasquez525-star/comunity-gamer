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
            $table->id(); // Equivale al id_usuario (Clave primaria autoincremental)
            $table->string('name'); // Nombre completo / de usuario
            $table->string('nombre_usuario', 50)->unique()->nullable(); // Campo personalizado para nickname/handle
            $table->string('email')->unique(); // Correo electrónico (utilizado por Laravel para auth)
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // Hash de la contraseña (reemplaza a contrasena_hash para compatibilidad)
            $table->string('avatar_url', 255)->nullable();
            $table->text('biografia')->nullable();
            $table->enum('estado', ['en_linea', 'ausente', 'desconectado'])->default('desconectado');
            $table->rememberToken();
            $table->timestamps(); // Genera created_at (reemplaza a fecha_registro) y updated_at
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
