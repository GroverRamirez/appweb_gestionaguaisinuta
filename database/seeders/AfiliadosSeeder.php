<?php

namespace Database\Seeders;

use App\Models\Afiliado;
use App\Services\GestionAguaService;
use Illuminate\Database\Seeder;

class AfiliadosSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(GestionAguaService::class);

        $afiliados = [
            [
                'ci' => '1234567',
                'nombres' => 'Juan Carlos',
                'apellidos' => 'Mercado Huerta',
                'telefono' => '70000001',
                'direccion' => 'Barrio Centro, Isinuta',
                'fecha_afiliacion' => '2018-03-15',
                'estado' => 'activo',
            ],
            [
                'ci' => '7654321',
                'nombres' => 'María Elena',
                'apellidos' => 'Vargas López',
                'telefono' => '70000002',
                'direccion' => 'OTB Barrio Centro',
                'fecha_afiliacion' => '2020-06-01',
                'estado' => 'activo',
            ],
            [
                'ci' => '9876543',
                'nombres' => 'Pedro',
                'apellidos' => 'Condori Mamani',
                'telefono' => '70000003',
                'direccion' => 'Isinuta, Villa Tunari',
                'fecha_afiliacion' => '2015-11-20',
                'estado' => 'activo',
            ],
        ];

        foreach ($afiliados as $data) {
            $afiliado = Afiliado::firstOrCreate(['ci' => $data['ci']], $data);
            $service->generarObligacionMensual($afiliado, (int) now()->format('n'), (int) now()->format('Y'));
        }
    }
}
