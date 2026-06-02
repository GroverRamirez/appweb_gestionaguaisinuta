<?php

namespace App\Services;

use App\Enums\AccionAuditoriaUsuario;
use App\Models\AuditoriaUsuario;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioAuditoriaService
{
    /**
     * @param  array<string, mixed>  $datos
     */
    public function registrar(
        User $actor,
        ?User $usuarioAfectado,
        AccionAuditoriaUsuario $accion,
        array $datos = [],
        ?Request $request = null,
    ): AuditoriaUsuario {
        return AuditoriaUsuario::query()->create([
            'usuario_id' => $actor->id,
            'usuario_afectado_id' => $usuarioAfectado?->id,
            'accion' => $accion,
            'datos' => $datos === [] ? null : $datos,
            'ip' => $request?->ip(),
            'created_at' => now(),
        ]);
    }
}
