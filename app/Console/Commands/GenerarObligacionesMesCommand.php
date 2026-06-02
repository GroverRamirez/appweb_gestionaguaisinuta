<?php

namespace App\Console\Commands;

use App\Services\GestionAguaService;
use Illuminate\Console\Command;

class GenerarObligacionesMesCommand extends Command
{
    protected $signature = 'isinuta:generar-obligaciones-mes';

    protected $description = 'Genera las obligaciones de pago del mes actual para todos los afiliados activos';

    public function handle(GestionAguaService $gestionAgua): int
    {
        $obligaciones = $gestionAgua->generarObligacionesMesActual();

        $this->info(sprintf(
            'Obligaciones del mes procesadas: %d registro(s).',
            $obligaciones->count(),
        ));

        return self::SUCCESS;
    }
}
