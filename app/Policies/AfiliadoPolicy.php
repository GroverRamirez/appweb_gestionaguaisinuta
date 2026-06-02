<?php

namespace App\Policies;

use App\Models\Afiliado;
use App\Models\User;

class AfiliadoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('afiliados.ver');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('afiliados.gestionar');
    }

    public function update(User $user, Afiliado $afiliado): bool
    {
        return $user->hasPermission('afiliados.gestionar');
    }

    public function delete(User $user, Afiliado $afiliado): bool
    {
        return $user->hasPermission('afiliados.gestionar');
    }
}
