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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('afiliado_id')->constrained('afiliados')->onDelete('cascade');
            $table->integer('mes');
            $table->integer('anio');
            $table->decimal('monto_agua', 8, 2)->default(8.00);
            $table->decimal('monto_alcantarillado', 8, 2)->default(15.00);
            $table->decimal('total', 8, 2)->default(23.00);
            $table->date('fecha_pago')->nullable();
            $table->string('estado')->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
