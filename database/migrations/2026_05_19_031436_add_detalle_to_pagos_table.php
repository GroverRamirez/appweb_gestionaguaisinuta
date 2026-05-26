<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->string('numero_recibo', 30)->nullable()->unique()->after('id');
            $table->foreignId('usuario_id')->nullable()->after('afiliado_id')->constrained('users')->nullOnDelete();
            $table->string('metodo', 30)->nullable()->after('fecha_pago');
            $table->text('observaciones')->nullable()->after('metodo');
            $table->unique(['afiliado_id', 'mes', 'anio']);
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropUnique(['afiliado_id', 'mes', 'anio']);
            $table->dropConstrainedForeignId('usuario_id');
            $table->dropColumn(['numero_recibo', 'metodo', 'observaciones']);
        });
    }
};
