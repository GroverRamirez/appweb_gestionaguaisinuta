<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
class HomeController extends Controller
{
    public function __invoke(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            $user->syncRolesFromLegacyColumn();
            $user->ensureRolesHavePermissions();

            if ($user->canAccessPanel() && $user->hasPermission('panel.ver')) {
                return redirect()->route('panel');
            }
        }

        return Inertia::render('Welcome', [
            'sinPermiso' => $user && ! $user->canAccessPanel(),
        ]);
    }
}
