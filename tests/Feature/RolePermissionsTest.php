<?php

use App\Models\Afiliado;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;

beforeEach(function () {
    $this->seed(PermissionsSeeder::class);
});

function userWithRole(string $roleName): User
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

test('admin can access afiliados management and reportes de deudas', function () {
    $user = userWithRole(Role::ADMIN);

    $this->actingAs($user)->get(route('afiliados.create'))->assertOk();
    $this->actingAs($user)->get(route('reportes.deudas'))->assertOk();
});

test('cajera can view afiliados but not create', function () {
    $user = userWithRole(Role::CAJERA);

    $this->actingAs($user)->get(route('afiliados.index'))->assertOk();
    $this->actingAs($user)->get(route('afiliados.create'))->assertForbidden();
});

test('cajera can register payments', function () {
    $user = userWithRole(Role::CAJERA);

    $this->actingAs($user)->get(route('pagos.create'))->assertOk();
});

test('cajera cannot approve tramites', function () {
    $user = userWithRole(Role::CAJERA);
    $afiliado = Afiliado::factory()->create();

    $tramite = $afiliado->tramites()->create([
        'ci_nuevo' => '8888888',
        'nombres_nuevo' => 'Nuevo',
        'apellidos_nuevo' => 'Titular',
        'sin_deudas_verificado' => true,
        'deuda_total_verificada' => 0,
        'estado' => 'pendiente',
    ]);

    $this->actingAs($user)
        ->post(route('tramites.aprobar', $tramite))
        ->assertForbidden();
});

test('cajera cannot view reporte de deudas', function () {
    $user = userWithRole(Role::CAJERA);

    $this->actingAs($user)->get(route('reportes.deudas'))->assertForbidden();
});

test('admin has all permissions via hasPermission', function () {
    $user = userWithRole(Role::ADMIN);

    expect($user->hasPermission('tramites.aprobar'))->toBeTrue();
    expect($user->hasPermission('afiliados.gestionar'))->toBeTrue();
});
