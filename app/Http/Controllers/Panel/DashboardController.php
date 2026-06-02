<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Afiliado;
use App\Models\Multa;
use App\Models\Pago;
use App\Models\Tramite;
use App\Services\GestionAguaService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private GestionAguaService $gestionAgua) {}

    public function index(Request $request): Response
    {
        $hoy = now();
        $mes = (int) $hoy->format('n');
        $anio = (int) $hoy->format('Y');

        $totales = [
            'afiliados_activos' => Afiliado::where('estado', 'activo')->count(),
            'pagos_pendientes' => Pago::where('estado', Pago::ESTADO_PENDIENTE)->count(),
            'monto_pendiente' => (float) Pago::where('estado', Pago::ESTADO_PENDIENTE)->sum('total'),
            'multas_pendientes' => Multa::where('estado', 'pendiente')->count(),
            'tramites_pendientes' => Tramite::where('estado', Tramite::ESTADO_PENDIENTE)->count(),
            'recaudacion_mes' => (float) Pago::where('estado', Pago::ESTADO_PAGADO)
                ->whereYear('fecha_pago', $anio)
                ->whereMonth('fecha_pago', $mes)
                ->sum('total'),
        ];

        $ultimosPagos = Pago::with(['afiliado:id,ci,nombres,apellidos'])
            ->where('estado', Pago::ESTADO_PAGADO)
            ->orderByDesc('fecha_pago')
            ->limit(8)
            ->get();

        return Inertia::render('Panel/Dashboard', [
            'totales' => $totales,
            'ultimos_pagos' => $ultimosPagos,
            'serie_recaudacion' => $this->gestionAgua->serieRecaudacion(),
        ]);
    }
}
