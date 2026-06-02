<?php

use App\Enums\AccionAuditoriaUsuario;
use App\Models\AuditoriaUsuario;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;

beforeEach(function () {
    $this->seed(PermissionsSeeder::class);
});

function auditoriaUser(string $roleName): User
{
    $user = User::factory()->create(['role' => $roleName]);
    $role = Role::where('nombre', $roleName)->firstOrFail();
    $user->roles()->sync([$role->id]);
    $user = $user->fresh();

    if ($roleName === Role::ADMIN) {
        $user = enableTwoFactorFor($user);
    }

    return $user;
}

test('creating a user records an audit log entry', function () {
    $admin = auditoriaUser(Role::ADMIN);

    $this->actingAs($admin)
        ->post(route('usuarios.store'), [
            'name' => 'Nueva Cajera',
            'email' => 'nueva.cajera@isinuta.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => Role::CAJERA,
        ])
        ->assertRedirect(route('usuarios.index'));

    $created = User::where('email', 'nueva.cajera@isinuta.test')->firstOrFail();

    $log = AuditoriaUsuario::query()->first();

    expect($log)->not->toBeNull()
        ->and($log->usuario_id)->toBe($admin->id)
        ->and($log->usuario_afectado_id)->toBe($created->id)
        ->and($log->accion)->toBe(AccionAuditoriaUsuario::Creado)
        ->and($log->datos)->toMatchArray([
            'name' => 'Nueva Cajera',
            'email' => 'nueva.cajera@isinuta.test',
            'role' => Role::CAJERA,
        ]);
});

test('updating a user records before and after values', function () {
    $admin = auditoriaUser(Role::ADMIN);
    $cajera = User::factory()->create([
        'name' => 'Cajera Original',
        'email' => 'cajera.original@isinuta.test',
        'role' => Role::CAJERA,
        'estado' => User::ESTADO_ACTIVO,
    ]);
    $cajera->roles()->sync([Role::where('nombre', Role::CAJERA)->firstOrFail()->id]);

    $this->actingAs($admin)
        ->put(route('usuarios.update', $cajera), [
            'name' => 'Cajera Actualizada',
            'email' => 'cajera.actualizada@isinuta.test',
            'role' => Role::CAJERA,
            'estado' => User::ESTADO_INACTIVO,
        ])
        ->assertRedirect(route('usuarios.index'));

    $log = AuditoriaUsuario::query()->latest('id')->first();

    expect($log)->not->toBeNull()
        ->and($log->accion)->toBe(AccionAuditoriaUsuario::Actualizado)
        ->and($log->datos['antes']['name'])->toBe('Cajera Original')
        ->and($log->datos['despues']['name'])->toBe('Cajera Actualizada')
        ->and($log->datos['despues']['estado'])->toBe(User::ESTADO_INACTIVO);
});

test('changing user estado records an audit log entry', function () {
    $admin = auditoriaUser(Role::ADMIN);
    $cajera = auditoriaUser(Role::CAJERA);

    $this->actingAs($admin)
        ->patch(route('usuarios.update-estado', $cajera), [
            'estado' => User::ESTADO_INACTIVO,
        ])
        ->assertSessionHasNoErrors();

    $log = AuditoriaUsuario::query()->first();

    expect($log)->not->toBeNull()
        ->and($log->accion)->toBe(AccionAuditoriaUsuario::EstadoCambiado)
        ->and($log->datos)->toMatchArray([
            'estado_anterior' => User::ESTADO_ACTIVO,
            'estado_nuevo' => User::ESTADO_INACTIVO,
        ]);
});

test('deleting own account records an audit log entry', function () {
    $role = Role::where('nombre', Role::ADMIN)->firstOrFail();

    $admin = auditoriaUser(Role::ADMIN);
    $otroAdmin = User::factory()->create(['role' => Role::ADMIN]);
    $otroAdmin->roles()->sync([$role->id]);

    $this->actingAs($admin)
        ->delete(route('profile.destroy'), [
            'password' => 'password',
        ])
        ->assertRedirect(route('home'));

    $log = AuditoriaUsuario::query()->first();

    expect($log)->not->toBeNull()
        ->and($log->accion)->toBe(AccionAuditoriaUsuario::Eliminado)
        ->and($log->datos['email'])->toBe($admin->email)
        ->and($log->datos['role'])->toBe(Role::ADMIN);
});

test('failed user actions do not create audit log entries', function () {
    $admin = auditoriaUser(Role::ADMIN);

    $this->actingAs($admin)
        ->post(route('usuarios.store'), [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'short',
            'role' => Role::CAJERA,
        ])
        ->assertSessionHasErrors();

    expect(AuditoriaUsuario::query()->count())->toBe(0);
});
