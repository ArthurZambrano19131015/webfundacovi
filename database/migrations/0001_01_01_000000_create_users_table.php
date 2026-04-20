<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. PRIMERO: Crear la tabla de Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_local')->unique();
            $table->boolean('synced')->default(true);
            $table->string('nombre_rol', 50)->unique();
            $table->timestamps();
        });

        // 2. SEGUNDO: Crear la tabla de Usuarios (users)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_local')->unique();
            $table->boolean('synced')->default(true);
            $table->foreignId('id_rol')->constrained('roles')->onDelete('restrict');
            $table->string('email', 100)->unique();
            $table->string('password'); 
            $table->string('nombre_completo', 100);
            $table->string('foto')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->boolean('estado_activo')->default(true);
            
            $table->rememberToken();
            $table->timestamps();
        });

        // Tablas del sistema de Laravel
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

    public function down(): void
    {
        // El orden al borrar es inverso: primero borramos users que depende de roles
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};