<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('usuarios.gestionar');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('usuarios.gestionar');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermission('usuarios.gestionar');
    }

    public function updateEstado(User $user, User $model): bool
    {
        return $user->hasPermission('usuarios.gestionar');
    }
}
