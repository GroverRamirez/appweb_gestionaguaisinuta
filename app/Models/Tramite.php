<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tramite extends Model
{
    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_APROBADO = 'aprobado';

    public const ESTADO_RECHAZADO = 'rechazado';

    protected $fillable = [
        'afiliado_id',
        'usuario_id',
        'ci_nuevo',
        'nombres_nuevo',
        'apellidos_nuevo',
        'estado',
        'sin_deudas_verificado',
        'deuda_total_verificada',
        'observaciones',
        'fecha_resolucion',
    ];

    protected function casts(): array
    {
        return [
            'sin_deudas_verificado' => 'boolean',
            'deuda_total_verificada' => 'decimal:2',
            'fecha_resolucion' => 'datetime',
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
}
