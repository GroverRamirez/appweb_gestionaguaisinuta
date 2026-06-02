<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Pago;
use App\Models\Tramite;
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

        $hoy = now();
        $mes = (int) $hoy->format('n');
        $anio = (int) $hoy->format('Y');

        return Inertia::render('Welcome', [
            'sinPermiso' => $user && ! $user->canAccessPanel(),
            'vistaPrevia' => [
                'afiliados_activos' => Afiliado::query()->where('estado', 'activo')->count(),
                'recaudacion_mes' => (float) Pago::query()
                    ->where('estado', Pago::ESTADO_PAGADO)
                    ->whereYear('fecha_pago', $anio)
                    ->whereMonth('fecha_pago', $mes)
                    ->sum('total'),
                'deuda_pendiente' => (float) Pago::query()
                    ->where('estado', Pago::ESTADO_PENDIENTE)
                    ->sum('total'),
                'tramites_pendientes' => Tramite::query()
                    ->where('estado', Tramite::ESTADO_PENDIENTE)
                    ->count(),
            ],
        ]);
    }
}
