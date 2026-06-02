<?php

namespace App\Http\Controllers;

use App\Enums\AccionAuditoriaUsuario;
use App\Models\AuditoriaUsuario;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UsuarioAuditoriaController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'accion' => ['nullable', 'string', Rule::enum(AccionAuditoriaUsuario::class)],
        ]);

        $registros = AuditoriaUsuario::query()
            ->with([
                'actor:id,name,email',
                'usuarioAfectado:id,name,email',
            ])
            ->when(
                isset($validated['accion']),
                fn ($query) => $query->where('accion', $validated['accion']),
            )
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Usuarios/Auditoria', [
            'registros' => $registros,
            'filtros' => $request->only('accion'),
            'acciones' => collect(AccionAuditoriaUsuario::cases())
                ->map(fn (AccionAuditoriaUsuario $accion) => [
                    'value' => $accion->value,
                    'label' => $accion->etiqueta(),
                ])
                ->values(),
        ]);
    }
}
