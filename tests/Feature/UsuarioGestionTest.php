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
    $user = $user->fresh();

    if ($roleName === Role::ADMIN) {
        $user = enableTwoFactorFor($user);
    }

    return $user;
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
        ->and($created->email_verified_at)->not->toBeNull();
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

test('inactive authenticated user is logged out on next request', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk();

    $user->update(['estado' => User::ESTADO_INACTIVO]);

    $this->get(route('profile.edit'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('inactive users cannot log in', function () {
    $user = User::factory()->inactivo()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('deactivated user loses access on next request while session was active', function () {
    $cajera = usuarioGestionUser(Role::CAJERA);

    $this->actingAs($cajera)
        ->get(route('pagos.index'))
        ->assertOk();

    $cajera->update(['estado' => User::ESTADO_INACTIVO]);

    $this->get(route('pagos.index'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('last active admin cannot delete their account', function () {
    $admin = usuarioGestionUser(Role::ADMIN);

    $this->actingAs($admin)
        ->from(route('profile.edit'))
        ->delete(route('profile.destroy'), [
            'password' => 'password',
        ])
        ->assertRedirect(route('profile.edit'));

    expect($admin->fresh())->not->toBeNull();
});

test('admin can delete their account when another active admin exists', function () {
    $role = Role::where('nombre', Role::ADMIN)->firstOrFail();

    $admin = usuarioGestionUser(Role::ADMIN);
    $otroAdmin = User::factory()->create(['role' => Role::ADMIN]);
    $otroAdmin->roles()->sync([$role->id]);

    $this->actingAs($admin)
        ->delete(route('profile.destroy'), [
            'password' => 'password',
        ])
        ->assertRedirect(route('home'));

    $this->assertGuest();
    expect($admin->fresh())->toBeNull();
    expect($otroAdmin->fresh())->not->toBeNull();
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
