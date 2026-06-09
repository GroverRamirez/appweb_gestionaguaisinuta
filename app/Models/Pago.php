<?php

namespace App\Models;

use Database\Factories\PagoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Pago extends Model
{
    /** @use HasFactory<PagoFactory> */
    use HasFactory;

    public const MONTO_AGUA = 8.00;

    public const MONTO_ALCANTARILLADO = 15.00;

    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_PAGADO = 'pagado';

    private const SECUENCIA_RECIBOS = 'pagos';

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
        return static::formatearNumeroRecibo(static::siguienteNumeroDisponible());
    }

    public static function reservarSiguienteNumeroRecibo(): string
    {
        return DB::transaction(function (): string {
            static::asegurarSecuenciaRecibos();

            $secuencia = DB::table('secuencias_recibos')
                ->where('nombre', self::SECUENCIA_RECIBOS)
                ->lockForUpdate()
                ->first();

            $numero = max(
                (int) $secuencia->siguiente_numero,
                static::mayorNumeroReciboAsignado() + 1,
            );

            DB::table('secuencias_recibos')
                ->where('nombre', self::SECUENCIA_RECIBOS)
                ->update([
                    'siguiente_numero' => $numero + 1,
                    'updated_at' => now(),
                ]);

            return static::formatearNumeroRecibo($numero);
        }, attempts: 5);
    }

    private static function siguienteNumeroDisponible(): int
    {
        $secuencia = DB::table('secuencias_recibos')
            ->where('nombre', self::SECUENCIA_RECIBOS)
            ->first();

        if ($secuencia) {
            return (int) $secuencia->siguiente_numero;
        }

        return static::mayorNumeroReciboAsignado() + 1;
    }

    private static function asegurarSecuenciaRecibos(): void
    {
        DB::table('secuencias_recibos')->insertOrIgnore([
            'nombre' => self::SECUENCIA_RECIBOS,
            'siguiente_numero' => static::mayorNumeroReciboAsignado() + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private static function mayorNumeroReciboAsignado(): int
    {
        return static::query()
            ->whereNotNull('numero_recibo')
            ->pluck('numero_recibo')
            ->map(fn (string $numeroRecibo): int => (int) preg_replace('/\D/', '', $numeroRecibo))
            ->max() ?? 0;
    }

    private static function formatearNumeroRecibo(int $numero): string
    {
        return 'REC-'.str_pad((string) $numero, 6, '0', STR_PAD_LEFT);
    }
}
