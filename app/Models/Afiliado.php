<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Afiliado extends Model
{
    /** @use HasFactory<\Database\Factories\AfiliadoFactory> */
    use HasFactory;

    protected $fillable = [
        'ci',
        'nombres',
        'apellidos',
        'telefono',
        'direccion',
        'fecha_afiliacion',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_afiliacion' => 'date',
        ];
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function multas(): HasMany
    {
        return $this->hasMany(Multa::class);
    }

    public function tramites(): HasMany
    {
        return $this->hasMany(Tramite::class);
    }

    public function nombreCompleto(): string
    {
        return trim("{$this->nombres} {$this->apellidos}");
    }
}
