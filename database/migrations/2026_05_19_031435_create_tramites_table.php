<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tramites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('afiliado_id')->constrained('afiliados')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ci_nuevo', 20);
            $table->string('nombres_nuevo');
            $table->string('apellidos_nuevo');
            $table->string('estado', 20)->default('pendiente');
            $table->boolean('sin_deudas_verificado')->default(false);
            $table->decimal('deuda_total_verificada', 10, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamp('fecha_resolucion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tramites');
    }
};
