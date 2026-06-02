<?php

use App\Models\Role;
use App\Models\User;
use App\Support\AuthUserPresenter;
use Database\Seeders\PermissionsSeeder;

beforeEach(function () {
    $this->seed(PermissionsSeeder::class);
});

function seguridadUser(string $roleName): User
{
    $user = User::factory()->create(['role' => $roleName, 'estado' => User::ESTADO_ACTIVO]);
    $role = Role::where('nombre', $roleName)->firstOrFail();
    $user->roles()->sync([$role->id]);
    $user = $user->fresh();

    if ($roleName === Role::ADMIN) {
        $user = enableTwoFactorFor($user);
    }

    return $user;
}

test('auth user presenter only exposes safe fields', function () {
    $user = User::factory()->create([
        'role' => Role::ADMIN,
        'remember_token' => 'secret-token',
    ]);

    $presented = AuthUserPresenter::forInertia($user);

    expect($presented)->toHaveKeys(['id', 'name', 'email', 'email_verified_at', 'two_factor_enabled'])
        ->and($presented)->not->toHaveKey('password')
        ->and($presented)->not->toHaveKey('remember_token')
        ->and($presented)->not->toHaveKey('two_factor_secret')
        ->and($presented)->not->toHaveKey('role')
        ->and($presented)->not->toHaveKey('estado');
});

test('inactive user cannot login', function () {
    $user = seguridadUser(Role::CAJERA);
    $user->update(['estado' => User::ESTADO_INACTIVO]);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('user without panel access cannot login', function () {
    $user = User::factory()->create(['role' => 'invitado', 'estado' => User::ESTADO_ACTIVO]);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('admin without two factor can still access panel', function () {
    $admin = seguridadUser(Role::ADMIN);
    $admin->forceFill([
        'two_factor_secret' => null,
        'two_factor_recovery_codes' => null,
        'two_factor_confirmed_at' => null,
    ])->save();

    $this->actingAs($admin->fresh())
        ->get(route('panel'))
        ->assertOk();
});

test('admin with two factor can access panel', function () {
    $admin = seguridadUser(Role::ADMIN);
    $admin->forceFill([
        'two_factor_secret' => encrypt('test-secret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['code1'])),
        'two_factor_confirmed_at' => now(),
    ])->save();

    $this->actingAs($admin->fresh())
        ->get(route('panel'))
        ->assertOk();
});

test('cajera is not required to enable two factor', function () {
    $cajera = seguridadUser(Role::CAJERA);

    $this->actingAs($cajera)
        ->get(route('panel'))
        ->assertOk();
});

test('security headers are present on web responses', function () {
    $response = $this->get(route('home'));

    $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
});

test('inertia shares sanitized auth user', function () {
    $admin = User::factory()->create(['role' => Role::ADMIN, 'estado' => User::ESTADO_ACTIVO]);
    $role = Role::where('nombre', Role::ADMIN)->firstOrFail();
    $admin->roles()->sync([$role->id]);

    $this->actingAs($admin)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('security.edit'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('auth.user.id', $admin->id)
            ->where('auth.user.email', $admin->email)
            ->missing('auth.user.password')
            ->missing('auth.user.two_factor_secret')
            ->where('auth.requires_two_factor_setup', true)
        );
});

test('user policy denies cajera from user management', function () {
    $cajera = seguridadUser(Role::CAJERA);

    $this->actingAs($cajera)
        ->get(route('usuarios.index'))
        ->assertForbidden();
});
