<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('rol_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('rol_id')->constrained('roles')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['usuario_id', 'rol_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rol_usuario');
        Schema::dropIfExists('roles');
    }
};
