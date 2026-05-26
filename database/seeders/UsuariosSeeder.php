<?php

namespace Database\Seeders;

use App\Actions\Teams\CreateTeam;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function __construct(private CreateTeam $createTeam) {}

    public function run(): void
    {
        $this->upsertUserWithRole([
            'email' => 'admin@isinuta.test',
            'name' => 'Administrador ISINUTA',
            'password' => 'admin1234',
            'role' => Role::ADMIN,
        ]);

        $this->upsertUserWithRole([
            'email' => 'cajera@isinuta.test',
            'name' => 'Cajera ISINUTA',
            'password' => 'cajera1234',
            'role' => Role::CAJERA,
        ]);

        $this->upsertUserWithRole([
            'email' => 'operador@isinuta.test',
            'name' => 'Operador ISINUTA (legado)',
            'password' => 'operador1234',
            'role' => Role::OPERADOR,
        ]);
    }

    /**
     * @param  array{email: string, name: string, password: string, role: string}  $data
     */
    private function upsertUserWithRole(array $data): void
    {
        $user = User::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
                'role' => $data['role'],
                'estado' => 'activo',
            ],
        );

        $user->assignRole($data['role']);

        if (! $user->personalTeam()) {
            $this->createTeam->handle($user, $user->name."'s Team", isPersonal: true);
        }
    }
}
