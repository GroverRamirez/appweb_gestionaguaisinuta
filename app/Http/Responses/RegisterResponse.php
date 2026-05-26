<?php

namespace App\Http\Responses;

use App\Http\Responses\Concerns\RedirectsUsersByRole;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
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
            ? new JsonResponse(['two_factor' => false], 201)
            : redirect()->to($this->homePathFor($request));
    }
}
