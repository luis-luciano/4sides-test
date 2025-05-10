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
        Schema::create('seg_usuario', function (Blueprint $table) {
            $table->id('idUsuario');
            $table->string('usuario_alias', 75)->nullable();
            $table->string('usuario_nombre', 100);
            $table->string('usuario_email', 100)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('usuario_password', 75);
            $table->enum('usuario_estado', ['Activo', 'Inactivo']);
            $table->boolean('usuario_conectado')->nullable();
            $table->string('profile_image')->nullable();
            $table->boolean('is_admin')->default(true);
            $table->timestamp('usuario_ultima_conexion')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('verification_code')->nullable();
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
        Schema::dropIfExists('seg_usuario');
        Schema::dropIfExists('password_reset');
        Schema::dropIfExists('sessions');
    }
};
