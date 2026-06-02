<?php

namespace App\Policies;

use App\Models\Afiliado;
use App\Models\Tramite;
use App\Models\User;

class TramitePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('tramites.ver');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('tramites.gestionar');
    }

    public function verificarDeudas(User $user, Afiliado $afiliado): bool
    {
        return $user->hasPermission('tramites.ver');
    }

    public function approve(User $user, Tramite $tramite): bool
    {
        return $user->hasPermission('tramites.aprobar');
    }

    public function reject(User $user, Tramite $tramite): bool
    {
        return $user->hasPermission('tramites.aprobar');
    }
}
