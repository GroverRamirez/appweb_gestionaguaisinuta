<?php

namespace App\Http\Middleware;

use App\Support\AuthUserPresenter;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * @see https://inertiajs.com/server-side-setup#root-template
     */
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        if ($user) {
            $user->loadMissing('roles.permisos');
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => fn () => AuthUserPresenter::forInertia($user),
                'roles' => fn () => $user ? $user->rolesNombres() : [],
                'permisos' => fn () => $user ? $user->permissionsNombres() : [],
                'etiqueta_rol' => fn () => $user?->etiquetaRol(),
                'isAdmin' => fn () => $user?->isAdmin() ?? false,
                'requires_two_factor_setup' => fn () => AuthUserPresenter::requiresTwoFactorSetup($user),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'info' => fn () => $request->session()->get('info'),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
