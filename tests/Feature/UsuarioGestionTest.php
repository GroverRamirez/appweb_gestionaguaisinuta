<?php

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->seed(PermissionsSeeder::class);
});

function usuarioGestionUser(string $roleName): User
{
    $user = User::factory()->create(['role' => $roleName]);
    $role = Role::where('nombre', $roleName)->firstOrFail();
    $user->roles()->sync([$role->id]);

    return $user->fresh();
}

test('admin can access user management', function () {
    $user = usuarioGestionUser(Role::ADMIN);

    $this->actingAs($user)
        ->get(route('usuarios.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Usuarios/Index'));
});

test('cajera cannot access user management', function () {
    $user = usuarioGestionUser(Role::CAJERA);

    $this->actingAs($user)->get(route('usuarios.index'))->assertForbidden();
});

test('admin has usuarios gestionar permission', function () {
    $user = usuarioGestionUser(Role::ADMIN);

    expect($user->hasPermission('usuarios.gestionar'))->toBeTrue();
});

test('cajera does not have usuarios gestionar permission', function () {
    $user = usuarioGestionUser(Role::CAJERA);

    expect($user->hasPermission('usuarios.gestionar'))->toBeFalse();
});

test('admin can access create user form', function () {
    $user = usuarioGestionUser(Role::ADMIN);

    $this->actingAs($user)
        ->get(route('usuarios.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Usuarios/Create'));
});

test('admin can create a new user', function () {
    $admin = usuarioGestionUser(Role::ADMIN);

    $response = $this->actingAs($admin)
        ->post(route('usuarios.store'), [
            'name' => 'Nueva Cajera',
            'email' => 'nueva.cajera@isinuta.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => Role::CAJERA,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('usuarios.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'nueva.cajera@isinuta.test',
        'name' => 'Nueva Cajera',
    ]);

    $created = User::where('email', 'nueva.cajera@isinuta.test')->first();

    expect($created)->not->toBeNull()
        ->and($created->hasRole(Role::CAJERA))->toBeTrue()
        ->and($created->email_verified_at)->not->toBeNull()
        ->and($created->personalTeam())->not->toBeNull()
        ->and($created->current_team_id)->not->toBeNull();
});

test('cajera cannot create users', function () {
    $user = usuarioGestionUser(Role::CAJERA);

    $this->actingAs($user)
        ->get(route('usuarios.create'))
        ->assertForbidden();

    $this->actingAs($user)
        ->post(route('usuarios.store'), [
            'name' => 'Intento',
            'email' => 'intento@isinuta.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => Role::CAJERA,
        ])
        ->assertForbidden();
});

test('create user validates unique email', function () {
    $admin = usuarioGestionUser(Role::ADMIN);
    User::factory()->create(['email' => 'duplicado@isinuta.test']);

    $this->actingAs($admin)
        ->post(route('usuarios.store'), [
            'name' => 'Duplicado',
            'email' => 'duplicado@isinuta.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => Role::CAJERA,
        ])
        ->assertSessionHasErrors('email');
});

test('edit form shows operador role for operador users', function () {
    $admin = usuarioGestionUser(Role::ADMIN);
    Role::firstOrCreate(
        ['nombre' => Role::OPERADOR],
        ['descripcion' => 'Operador del sistema ISINUTA'],
    );
    $operador = usuarioGestionUser(Role::OPERADOR);

    $this->actingAs($admin)
        ->get(route('usuarios.edit', $operador))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Usuarios/Edit')
            ->where('usuario.role', Role::OPERADOR)
            ->where('roles_disponibles', fn ($roles) => collect($roles)->pluck('value')->contains(Role::OPERADOR))
        );
});

test('admin can deactivate another user from index', function () {
    $admin = usuarioGestionUser(Role::ADMIN);
    $cajera = usuarioGestionUser(Role::CAJERA);

    $this->actingAs($admin)
        ->patch(route('usuarios.update-estado', $cajera), ['estado' => 'inactivo'])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($cajera->fresh()->estado)->toBe('inactivo');
});

test('admin cannot deactivate their own account', function () {
    $admin = usuarioGestionUser(Role::ADMIN);

    $this->actingAs($admin)
        ->patch(route('usuarios.update-estado', $admin), ['estado' => 'inactivo'])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($admin->fresh()->estado)->toBe('activo');
});

test('inactive users cannot log in', function () {
    $user = User::factory()->inactivo()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('user index includes estado for each user', function () {
    $admin = usuarioGestionUser(Role::ADMIN);

    $this->actingAs($admin)
        ->get(route('usuarios.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Usuarios/Index')
            ->has('usuarios.data.0.estado')
        );
});
