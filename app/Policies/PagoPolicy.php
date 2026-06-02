<?php

namespace App\Policies;

use App\Models\Pago;
use App\Models\User;

class PagoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('pagos.ver');
    }

    public function view(User $user, Pago $pago): bool
    {
        return $user->hasPermission('pagos.ver');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('pagos.gestionar');
    }

    public function buscarAfiliado(User $user): bool
    {
        return $user->hasPermission('pagos.gestionar');
    }

    public function generarMes(User $user): bool
    {
        return $user->hasPermission('pagos.gestionar');
    }
}
