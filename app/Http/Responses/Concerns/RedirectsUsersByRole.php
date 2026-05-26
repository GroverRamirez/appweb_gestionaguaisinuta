<?php

namespace App\Http\Responses\Concerns;

use Illuminate\Http\Request;

trait RedirectsUsersByRole
{
    protected function homePathFor(Request $request): string
    {
        $user = $request->user();

        if (! $user) {
            return route('home', absolute: false);
        }

        $user->syncRolesFromLegacyColumn();
        $user->ensureRolesHavePermissions();

        if (! $user->canAccessPanel()) {
            return route('home', absolute: false);
        }

        if ($user->hasPermission('panel.ver')) {
            return route('panel', absolute: false);
        }

        foreach ([
            'pagos.ver' => 'pagos.index',
            'afiliados.ver' => 'afiliados.index',
            'multas.ver' => 'multas.index',
            'tramites.ver' => 'tramites.index',
            'reportes.ver' => 'reportes.recaudacion',
        ] as $permission => $routeName) {
            if ($user->hasPermission($permission)) {
                return route($routeName, absolute: false);
            }
        }

        return route('panel', absolute: false);
    }
}
