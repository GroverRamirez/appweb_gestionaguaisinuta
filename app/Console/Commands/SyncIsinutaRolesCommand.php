<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Console\Command;

class SyncIsinutaRolesCommand extends Command
{
    protected $signature = 'isinuta:sync-roles';

    protected $description = 'Sincroniza roles, permisos y asignaciones de usuarios ISINUTA';

    public function handle(): int
    {
        $this->call('db:seed', ['--class' => PermissionsSeeder::class, '--no-interaction' => true]);

        $count = 0;

        User::query()->each(function (User $user) use (&$count) {
            $user->syncRolesFromLegacyColumn();

            if ($user->roles()->doesntExist() && $user->legacyRoleName() === null) {
                $user->assignRole(Role::CAJERA);
                $this->line("Rol cajera asignado a: {$user->email}");
                $count++;
            }
        });

        $this->info("Listo. {$count} usuario(s) actualizados.");

        return self::SUCCESS;
    }
}
