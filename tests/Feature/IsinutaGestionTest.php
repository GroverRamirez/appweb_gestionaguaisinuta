<?php

use App\Models\Afiliado;
use App\Models\Pago;
use App\Models\Role;
use App\Models\User;
use App\Services\GestionAguaService;
use Database\Seeders\PermissionsSeeder;

beforeEach(function () {
    $this->seed(PermissionsSeeder::class);
});

test('user with legacy operator column can access panel', function () {
    $user = User::factory()->create(['role' => 'operator']);
    $user->syncRolesFromLegacyColumn();

    expect($user->canAccessPanel())->toBeTrue();

    $this->actingAs($user)->get(route('panel'))->assertOk();
});

test('guest cannot access panel', function () {
    $this->get(route('panel'))->assertRedirect(route('login'));
});

test('user without role cannot access panel', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('panel'))->assertForbidden();
});

test('admin can access panel and afiliados', function () {
    $user = User::factory()->create(['role' => Role::ADMIN]);
    $role = Role::firstOrCreate(['nombre' => Role::ADMIN], ['descripcion' => 'Admin']);
    $user->roles()->attach($role);
    $user = enableTwoFactorFor($user->fresh());

    $this->actingAs($user)->get(route('panel'))->assertOk();
    $this->actingAs($user)->get(route('afiliados.index'))->assertOk();
});

test('can register monthly payment for afiliado', function () {
    $user = User::factory()->create();
    $role = Role::where('nombre', Role::CAJERA)->firstOrFail();
    $user->roles()->sync([$role->id]);

    $afiliado = Afiliado::factory()->create(['estado' => 'activo']);
    $pago = app(GestionAguaService::class)->generarObligacionMensual(
        $afiliado,
        (int) now()->format('n'),
        (int) now()->format('Y'),
    );

    $response = $this->actingAs($user)->post(route('pagos.store'), [
        'pago_id' => $pago->id,
        'fecha_pago' => now()->toDateString(),
        'metodo' => 'efectivo',
    ]);

    $response->assertRedirect();
    expect($pago->fresh()->estado)->toBe(Pago::ESTADO_PAGADO);
    expect($pago->fresh()->numero_recibo)->not->toBeNull();
});

test('tramite requires no debts to approve', function () {
    $user = User::factory()->create(['role' => Role::ADMIN]);
    $role = Role::firstOrCreate(['nombre' => Role::ADMIN], ['descripcion' => 'Admin']);
    $user->roles()->attach($role);
    $user = enableTwoFactorFor($user->fresh());

    $afiliado = Afiliado::factory()->create();
    app(GestionAguaService::class)->generarObligacionMensual(
        $afiliado,
        (int) now()->format('n'),
        (int) now()->format('Y'),
    );

    $tramite = $afiliado->tramites()->create([
        'ci_nuevo' => '9999999',
        'nombres_nuevo' => 'Nuevo',
        'apellidos_nuevo' => 'Titular',
        'sin_deudas_verificado' => false,
        'deuda_total_verificada' => 23,
        'estado' => 'pendiente',
    ]);

    $this->actingAs($user)
        ->post(route('tramites.aprobar', $tramite))
        ->assertSessionHas('error');

    expect($afiliado->fresh()->nombres)->not->toBe('Nuevo');
});
