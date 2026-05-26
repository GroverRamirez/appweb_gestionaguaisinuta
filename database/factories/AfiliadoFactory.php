<?php

namespace Database\Factories;

use App\Models\Afiliado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Afiliado>
 */
class AfiliadoFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        fake()->locale('es_ES');

        return [
            'ci' => fake()->unique()->numerify('#######'),
            'nombres' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'telefono' => fake()->numerify('7#######'),
            'direccion' => 'Barrio '.fake()->streetName().', Isinuta',
            'fecha_afiliacion' => fake()->date(),
            'estado' => 'activo',
        ];
    }
}
