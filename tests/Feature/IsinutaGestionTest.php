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

test('receipt numbers continue sequentially when old pending payments are charged', function () {
    $user = User::factory()->create();
    $role = Role::where('nombre', Role::CAJERA)->firstOrFail();
    $user->roles()->sync([$role->id]);

    $afiliado = Afiliado::factory()->create(['estado' => 'activo']);

    $pagoAntiguo = Pago::create([
        'afiliado_id' => $afiliado->id,
        'mes' => 1,
        'anio' => 2026,
        'monto_agua' => Pago::MONTO_AGUA,
        'monto_alcantarillado' => Pago::MONTO_ALCANTARILLADO,
        'total' => Pago::MONTO_AGUA + Pago::MONTO_ALCANTARILLADO,
        'estado' => Pago::ESTADO_PENDIENTE,
    ]);

    Pago::create([
        'numero_recibo' => 'REC-000010',
        'afiliado_id' => $afiliado->id,
        'mes' => 2,
        'anio' => 2026,
        'monto_agua' => Pago::MONTO_AGUA,
        'monto_alcantarillado' => Pago::MONTO_ALCANTARILLADO,
        'total' => Pago::MONTO_AGUA + Pago::MONTO_ALCANTARILLADO,
        'fecha_pago' => now()->toDateString(),
        'metodo' => 'efectivo',
        'estado' => Pago::ESTADO_PAGADO,
    ]);

    $pagoNuevo = Pago::create([
        'afiliado_id' => $afiliado->id,
        'mes' => 3,
        'anio' => 2026,
        'monto_agua' => Pago::MONTO_AGUA,
        'monto_alcantarillado' => Pago::MONTO_ALCANTARILLADO,
        'total' => Pago::MONTO_AGUA + Pago::MONTO_ALCANTARILLADO,
        'estado' => Pago::ESTADO_PENDIENTE,
    ]);

    $this->actingAs($user)
        ->post(route('pagos.store'), [
            'pago_id' => $pagoAntiguo->id,
            'fecha_pago' => now()->toDateString(),
            'metodo' => 'efectivo',
        ])
        ->assertRedirect();

    $this->actingAs($user)
        ->post(route('pagos.store'), [
            'pago_id' => $pagoNuevo->id,
            'fecha_pago' => now()->toDateString(),
            'metodo' => 'efectivo',
        ])
        ->assertRedirect();

    expect($pagoAntiguo->fresh()->numero_recibo)->toBe('REC-000011');
    expect($pagoNuevo->fresh()->numero_recibo)->toBe('REC-000012');
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
