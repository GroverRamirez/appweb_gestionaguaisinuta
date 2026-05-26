<?php

namespace App\Services;

use App\Models\Afiliado;
use App\Models\Multa;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GestionAguaService
{
    public const MULTA_TRIMESTRAL = 50.00;

    public const MULTA_ANUAL = 150.00;

    /**
     * @return array{pagos: float, multas: float, total: float, meses_pendientes: int}
     */
    public function calcularDeudaAfiliado(Afiliado $afiliado): array
    {
        $pagosPendientes = (float) $afiliado->pagos()
            ->where('estado', Pago::ESTADO_PENDIENTE)
            ->sum('total');

        $multasPendientes = (float) $afiliado->multas()
            ->where('estado', 'pendiente')
            ->sum('monto');

        $mesesPendientes = $afiliado->pagos()
            ->where('estado', Pago::ESTADO_PENDIENTE)
            ->count();

        return [
            'pagos' => $pagosPendientes,
            'multas' => $multasPendientes,
            'total' => $pagosPendientes + $multasPendientes,
            'meses_pendientes' => $mesesPendientes,
        ];
    }

    public function afiliadoSinDeudas(Afiliado $afiliado): bool
    {
        return $this->calcularDeudaAfiliado($afiliado)['total'] <= 0;
    }

    public function generarObligacionMensual(Afiliado $afiliado, int $mes, int $anio): Pago
    {
        return Pago::firstOrCreate(
            [
                'afiliado_id' => $afiliado->id,
                'mes' => $mes,
                'anio' => $anio,
            ],
            [
                'monto_agua' => Pago::MONTO_AGUA,
                'monto_alcantarillado' => Pago::MONTO_ALCANTARILLADO,
                'total' => Pago::MONTO_AGUA + Pago::MONTO_ALCANTARILLADO,
                'estado' => Pago::ESTADO_PENDIENTE,
            ],
        );
    }

    /**
     * @return Collection<int, Pago>
     */
    public function generarObligacionesMesActual(): Collection
    {
        $hoy = now();
        $mes = (int) $hoy->format('n');
        $anio = (int) $hoy->format('Y');

        return Afiliado::query()
            ->where('estado', 'activo')
            ->get()
            ->map(fn (Afiliado $afiliado) => $this->generarObligacionMensual($afiliado, $mes, $anio));
    }

    public function sugerirMulta(Afiliado $afiliado): ?array
    {
        $mesesPendientes = $afiliado->pagos()
            ->where('estado', Pago::ESTADO_PENDIENTE)
            ->count();

        if ($mesesPendientes >= 12) {
            return [
                'tipo' => Multa::TIPO_ANUAL,
                'monto' => self::MULTA_ANUAL,
                'meses_mora' => $mesesPendientes,
            ];
        }

        if ($mesesPendientes >= 3) {
            return [
                'tipo' => Multa::TIPO_TRIMESTRAL,
                'monto' => self::MULTA_TRIMESTRAL,
                'meses_mora' => $mesesPendientes,
            ];
        }

        return null;
    }

    /**
     * @return array{periodo: string, monto: float}
     */
    public function serieRecaudacion(int $meses = 6): array
    {
        $serie = [];
        $hoy = now();

        for ($i = $meses - 1; $i >= 0; $i--) {
            $fecha = $hoy->copy()->subMonths($i)->locale('es');
            $serie[] = [
                'periodo' => $fecha->translatedFormat('M Y'),
                'monto' => (float) Pago::query()
                    ->where('estado', Pago::ESTADO_PAGADO)
                    ->whereYear('fecha_pago', $fecha->year)
                    ->whereMonth('fecha_pago', $fecha->month)
                    ->sum('total'),
            ];
        }

        return $serie;
    }

    public function mesesAtrasoDesde(Carbon $desde, Carbon $hasta): int
    {
        return max(0, (int) $desde->diffInMonths($hasta));
    }
}
