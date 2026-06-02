<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Fortify\Features;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminHasTwoFactor
{
    /**
     * @var array<int, string>
     */
    private const EXEMPT_ROUTE_NAMES = [
        'logout',
        'security.edit',
        'user-password.update',
        'password.confirm',
        'password.confirm.store',
        'password.confirmation',
        'two-factor.login',
        'two-factor.login.store',
        'two-factor.enable',
        'two-factor.disable',
        'two-factor.confirm',
        'two-factor.qr-code',
        'two-factor.recovery-codes',
        'two-factor.regenerate-recovery-codes',
        'two-factor.secret-key',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! Features::canManageTwoFactorAuthentication()) {
            return $next($request);
        }

        if (! $user->isAdmin() || $user->hasEnabledTwoFactorAuthentication()) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();

        if ($routeName !== null && in_array($routeName, self::EXEMPT_ROUTE_NAMES, true)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403, 'Debe activar la verificación en dos pasos para continuar.');
        }

        return redirect()
            ->route('security.edit')
            ->with('info', 'Como administrador debe activar la verificación en dos pasos antes de continuar.');
    }
}
