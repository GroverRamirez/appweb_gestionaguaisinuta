<?php

use App\Http\Controllers\AfiliadoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MultaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\Panel\DashboardController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', HomeController::class)->name('home');

Route::middleware(['auth', 'verified', 'role:admin,cajera,operador'])->group(function () {
    Route::get('panel', [DashboardController::class, 'index'])
        ->middleware('permission:panel.ver')
        ->name('panel');

    Route::middleware('permission:afiliados.ver')->group(function () {
        Route::get('afiliados', [AfiliadoController::class, 'index'])->name('afiliados.index');

        Route::middleware('permission:afiliados.gestionar')->group(function () {
            Route::get('afiliados/crear', [AfiliadoController::class, 'create'])->name('afiliados.create');
            Route::post('afiliados', [AfiliadoController::class, 'store'])->name('afiliados.store');
            Route::get('afiliados/{afiliado}/editar', [AfiliadoController::class, 'edit'])->name('afiliados.edit');
            Route::put('afiliados/{afiliado}', [AfiliadoController::class, 'update'])->name('afiliados.update');
            Route::delete('afiliados/{afiliado}', [AfiliadoController::class, 'destroy'])->name('afiliados.destroy');
        });
    });

    Route::middleware('permission:pagos.ver')->group(function () {
        Route::get('pagos', [PagoController::class, 'index'])->name('pagos.index');

        Route::middleware('permission:pagos.gestionar')->group(function () {
            Route::get('pagos/crear', [PagoController::class, 'create'])->name('pagos.create');
            Route::post('pagos', [PagoController::class, 'store'])->name('pagos.store');
            Route::get('pagos/buscar-afiliado', [PagoController::class, 'buscarAfiliado'])->name('pagos.buscar-afiliado');
            Route::post('pagos/generar-mes', [PagoController::class, 'generarMes'])->name('pagos.generar-mes');
        });

        Route::get('pagos/{pago}', [PagoController::class, 'show'])->name('pagos.show');
    });

    Route::middleware('permission:multas.ver')->group(function () {
        Route::get('multas', [MultaController::class, 'index'])->name('multas.index');

        Route::middleware('permission:multas.gestionar')->group(function () {
            Route::get('multas/crear', [MultaController::class, 'create'])->name('multas.create');
            Route::post('multas', [MultaController::class, 'store'])->name('multas.store');
            Route::post('multas/{multa}/pagar', [MultaController::class, 'marcarPagada'])->name('multas.pagar');
        });
    });

    Route::middleware('permission:tramites.ver')->group(function () {
        Route::get('tramites', [TramiteController::class, 'index'])->name('tramites.index');
        Route::get('tramites/verificar/{afiliado}', [TramiteController::class, 'verificarDeudas'])->name('tramites.verificar');

        Route::middleware('permission:tramites.gestionar')->group(function () {
            Route::get('tramites/crear', [TramiteController::class, 'create'])->name('tramites.create');
            Route::post('tramites', [TramiteController::class, 'store'])->name('tramites.store');
        });

        Route::middleware('permission:tramites.aprobar')->group(function () {
            Route::post('tramites/{tramite}/aprobar', [TramiteController::class, 'aprobar'])->name('tramites.aprobar');
            Route::post('tramites/{tramite}/rechazar', [TramiteController::class, 'rechazar'])->name('tramites.rechazar');
        });
    });

    Route::middleware('permission:reportes.ver')->group(function () {
        Route::get('reportes/recaudacion', [ReporteController::class, 'recaudacion'])->name('reportes.recaudacion');
    });

    Route::middleware('permission:reportes.deudas')->group(function () {
        Route::get('reportes/afiliados', [ReporteController::class, 'afiliados'])->name('reportes.afiliados');
        Route::get('reportes/deudas', [ReporteController::class, 'deudas'])->name('reportes.deudas');
    });

    Route::middleware('permission:usuarios.gestionar')->group(function () {
        Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::patch('usuarios/{usuario}/estado', [UsuarioController::class, 'updateEstado'])->name('usuarios.update-estado');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
});

require __DIR__.'/settings.php';
