<?php

namespace App\Models;

use App\Enums\AccionAuditoriaUsuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditoriaUsuario extends Model
{
    public $timestamps = false;

    protected $table = 'auditoria_usuarios';

    protected $fillable = [
        'usuario_id',
        'usuario_afectado_id',
        'accion',
        'datos',
        'ip',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'accion' => AccionAuditoriaUsuario::class,
            'datos' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function usuarioAfectado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_afectado_id');
    }
}
