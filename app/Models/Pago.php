<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    /** @use HasFactory<\Database\Factories\PagoFactory> */
    use HasFactory;

    public const MONTO_AGUA = 8.00;

    public const MONTO_ALCANTARILLADO = 15.00;

    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_PAGADO = 'pagado';

    protected $fillable = [
        'numero_recibo',
        'afiliado_id',
        'usuario_id',
        'mes',
        'anio',
        'monto_agua',
        'monto_alcantarillado',
        'total',
        'fecha_pago',
        'metodo',
        'estado',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'monto_agua' => 'decimal:2',
            'monto_alcantarillado' => 'decimal:2',
            'total' => 'decimal:2',
            'fecha_pago' => 'date',
        ];
    }

    public function afiliado(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public static function siguienteNumero(): string
    {
        $ultimo = static::query()
            ->whereNotNull('numero_recibo')
            ->orderByDesc('id')
            ->value('numero_recibo');

        $numero = $ultimo ? (int) preg_replace('/\D/', '', $ultimo) + 1 : 1;

        return 'REC-'.str_pad((string) $numero, 6, '0', STR_PAD_LEFT);
    }
}
