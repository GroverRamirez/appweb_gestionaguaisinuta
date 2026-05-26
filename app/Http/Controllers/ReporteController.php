<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Multa;
use App\Models\Pago;
use App\Services\GestionAguaService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReporteController extends Controller
{
    public function __construct(private GestionAguaService $gestionAgua) {}

    public function afiliados(): Response
    {
        $afiliados = Afiliado::query()
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get(['id', 'ci', 'nombres', 'apellidos', 'telefono', 'estado', 'fecha_afiliacion']);

        return Inertia::render('Reportes/Afiliados', [
            'afiliados' => $afiliados,
            'resumen' => [
                'activos' => $afiliados->where('estado', 'activo')->count(),
                'inactivos' => $afiliados->where('estado', 'inactivo')->count(),
                'total' => $afiliados->count(),
            ],
        ]);
    }

    public function recaudacion(Request $request): Response
    {
        $mes = (int) ($request->input('mes') ?: now()->format('n'));
        $anio = (int) ($request->input('anio') ?: now()->format('Y'));

        $pagos = Pago::with(['afiliado:id,ci,nombres,apellidos'])
            ->where('estado', Pago::ESTADO_PAGADO)
            ->where('mes', $mes)
            ->where('anio', $anio)
            ->orderBy('fecha_pago')
            ->get();

        return Inertia::render('Reportes/Recaudacion', [
            'pagos' => $pagos,
            'filtros' => compact('mes', 'anio'),
            'total' => (float) $pagos->sum('total'),
            'serie' => $this->gestionAgua->serieRecaudacion(12),
        ]);
    }

    public function deudas(): Response
    {
        $afiliados = Afiliado::query()
            ->where('estado', 'activo')
            ->withCount([
                'pagos as pagos_pendientes' => fn ($q) => $q->where('estado', Pago::ESTADO_PENDIENTE),
            ])
            ->get()
            ->map(function (Afiliado $afiliado) {
                $deuda = $this->gestionAgua->calcularDeudaAfiliado($afiliado);

                return [
                    'id' => $afiliado->id,
                    'ci' => $afiliado->ci,
                    'nombres' => $afiliado->nombres,
                    'apellidos' => $afiliado->apellidos,
                    'pagos_pendientes' => $deuda['meses_pendientes'],
                    'deuda_pagos' => $deuda['pagos'],
                    'deuda_multas' => $deuda['multas'],
                    'deuda_total' => $deuda['total'],
                ];
            })
            ->filter(fn (array $row) => $row['deuda_total'] > 0)
            ->sortByDesc('deuda_total')
            ->values();

        return Inertia::render('Reportes/Deudas', [
            'afiliados' => $afiliados,
            'total_deuda' => (float) $afiliados->sum('deuda_total'),
            'multas_pendientes' => (float) Multa::where('estado', 'pendiente')->sum('monto'),
        ]);
    }
}
