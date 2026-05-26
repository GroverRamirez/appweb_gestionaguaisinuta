<?php

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Models\Permiso;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PermissionEnum::cases() as $permission) {
            Permiso::updateOrCreate(
                ['nombre' => $permission->value],
                [
                    'descripcion' => $permission->label(),
                    'grupo' => $permission->group(),
                ],
            );
        }

        $admin = Role::firstOrCreate(
            ['nombre' => Role::ADMIN],
            ['descripcion' => 'Administrador con acceso total al sistema'],
        );
        $admin->sincronizarPermisosPorNombre(PermissionEnum::valuesForAdministrador());

        $cajera = Role::firstOrCreate(
            ['nombre' => Role::CAJERA],
            ['descripcion' => 'Cajera con acceso parcial según permisos asignados'],
        );
        $cajera->sincronizarPermisosPorNombre(PermissionEnum::valuesForCajera());

        $operador = Role::query()->where('nombre', Role::OPERADOR)->first();
        if ($operador) {
            $operador->sincronizarPermisosPorNombre(PermissionEnum::valuesForCajera());
        }
    }
}
