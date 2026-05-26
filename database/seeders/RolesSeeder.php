<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nombre' => Role::ADMIN,
                'descripcion' => 'Administrador: acceso total al sistema',
            ],
            [
                'nombre' => Role::CAJERA,
                'descripcion' => 'Cajera: acceso parcial según permisos del rol',
            ],
            [
                'nombre' => Role::OPERADOR,
                'descripcion' => 'Operador (legado, mismos permisos que cajera)',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['nombre' => $role['nombre']], $role);
        }
    }
}
