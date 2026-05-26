<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80)->unique();
            $table->string('descripcion')->nullable();
            $table->string('grupo', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('permiso_rol', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permiso_id')->constrained('permisos')->cascadeOnDelete();
            $table->foreignId('rol_id')->constrained('roles')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['permiso_id', 'rol_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permiso_rol');
        Schema::dropIfExists('permisos');
    }
};
