<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Multa extends Model
{
    public const TIPO_TRIMESTRAL = 'trimestral';

    public const TIPO_ANUAL = 'anual';

    protected $fillable = [
        'afiliado_id',
        'usuario_id',
        'tipo',
        'monto',
        'meses_mora',
        'fecha_aplicacion',
        'estado',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'fecha_aplicacion' => 'date',
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
