<?php

namespace App\Support;

use App\Models\User;
use Laravel\Fortify\Features;

class AuthUserPresenter
{
    /**
     * Datos mínimos del usuario autenticado para el frontend (sin campos sensibles).
     *
     * @return array<string, mixed>|null
     */
    public static function forInertia(?User $user): ?array
    {
        if ($user === null) {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
            'two_factor_enabled' => Features::canManageTwoFactorAuthentication()
                && $user->hasEnabledTwoFactorAuthentication(),
        ];
    }

    public static function requiresTwoFactorSetup(?User $user): bool
    {
        if ($user === null || ! Features::canManageTwoFactorAuthentication()) {
            return false;
        }

        return $user->isAdmin() && ! $user->hasEnabledTwoFactorAuthentication();
    }
}
