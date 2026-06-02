<?php

use App\Enums\AccionAuditoriaUsuario;
use App\Models\Afiliado;
use App\Models\Pago;
use App\Models\Role;
use App\Models\Tramite;
use App\Models\User;
use App\Services\GestionAguaService;
use App\Services\UsuarioAuditoriaService;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->seed(PermissionsSeeder::class);
});

function politicasUser(string $roleName): User
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

test('cajera cannot create afiliados via policy', function () {
    $cajera = politicasUser(Role::CAJERA);

    $this->actingAs($cajera)
        ->post(route('afiliados.store'), [
            'ci' => '9999999',
            'nombres' => 'Test',
            'apellidos' => 'Usuario',
        ])
        ->assertForbidden();
});

test('cajera cannot approve tramites', function () {
    $cajera = politicasUser(Role::CAJERA);
    $afiliado = Afiliado::factory()->create();
    $tramite = $afiliado->tramites()->create([
        'ci_nuevo' => '8888888',
        'nombres_nuevo' => 'Nuevo',
        'apellidos_nuevo' => 'Titular',
        'sin_deudas_verificado' => true,
        'deuda_total_verificada' => 0,
        'estado' => Tramite::ESTADO_PENDIENTE,
    ]);

    $this->actingAs($cajera)
        ->post(route('tramites.aprobar', $tramite))
        ->assertForbidden();
});

test('panel dashboard does not auto generate monthly obligations', function () {
    $admin = politicasUser(Role::ADMIN);
    $afiliado = Afiliado::factory()->create(['estado' => 'activo']);
    $hoy = now();

    app(GestionAguaService::class)->generarObligacionMensual(
        $afiliado,
        (int) $hoy->format('n'),
        (int) $hoy->format('Y'),
    );

    $antes = Pago::query()->where('afiliado_id', $afiliado->id)->count();

    $this->actingAs($admin)->get(route('panel'))->assertOk();

    expect(Pago::query()->where('afiliado_id', $afiliado->id)->count())->toBe($antes);
});

test('generar obligaciones mes command creates obligations', function () {
    $afiliado = Afiliado::factory()->create(['estado' => 'activo']);

    Artisan::call('isinuta:generar-obligaciones-mes');

    expect(
        Pago::query()
            ->where('afiliado_id', $afiliado->id)
            ->where('mes', (int) now()->format('n'))
            ->where('anio', (int) now()->format('Y'))
            ->exists(),
    )->toBeTrue();
});

test('admin can view usuario auditoria index', function () {
    $admin = politicasUser(Role::ADMIN);
    $afectado = User::factory()->create();

    app(UsuarioAuditoriaService::class)->registrar(
        $admin,
        $afectado,
        AccionAuditoriaUsuario::Actualizado,
    );

    $this->actingAs($admin)
        ->get(route('usuarios.auditoria'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Usuarios/Auditoria'));
});

test('cajera cannot view usuario auditoria', function () {
    $cajera = politicasUser(Role::CAJERA);

    $this->actingAs($cajera)
        ->get(route('usuarios.auditoria'))
        ->assertForbidden();
});
