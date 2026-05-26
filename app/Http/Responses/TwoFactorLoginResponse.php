<?php

namespace App\Http\Responses;

use App\Http\Responses\Concerns\RedirectsUsersByRole;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorLoginResponse implements TwoFactorLoginResponseContract
{
    use RedirectsUsersByRole;

    public function toResponse($request): Response
    {
        $user = $request->user();

        if ($user) {
            $user->syncRolesFromLegacyColumn();
            $user->ensureRolesHavePermissions();
        }

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 200)
            : redirect()->to($this->homePathFor($request));
    }
}
