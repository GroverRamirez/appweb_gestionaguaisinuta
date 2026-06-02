<?php

namespace App\Policies;

use App\Models\Multa;
use App\Models\User;

class MultaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('multas.ver');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('multas.gestionar');
    }

    public function marcarPagada(User $user, Multa $multa): bool
    {
        return $user->hasPermission('multas.gestionar');
    }
}
