<?php

namespace App\Http\Controllers;

use App\Actions\Teams\CreateTeam;
use App\Models\Role;
use App\Models\User;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioEstadoRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class UsuarioController extends Controller
{
    public function __construct(private CreateTeam $createTeam) {}
    public function index(Request $request): Response
    {
        $query = User::query()->with('roles');

        if ($request->filled('busqueda')) {
            $busqueda = $request->string('busqueda')->toString();
            $query->where(function ($q) use ($busqueda) {
                $q->where('name', 'like', "%{$busqueda}%")
                    ->orWhere('email', 'like', "%{$busqueda}%");
            });
        }

        $usuarios = $query->orderBy('name')->paginate(12)->withQueryString();

        $usuarios->getCollection()->transform(function (User $user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->rolesNombres(),
                'etiqueta_rol' => $user->etiquetaRol(),
                'estado' => $user->estado ?? User::ESTADO_ACTIVO,
                'email_verified_at' => $user->email_verified_at?->toDateString(),
            ];
        });

        return Inertia::render('Usuarios/Index', [
            'usuarios' => $usuarios,
            'filtros' => $request->only('busqueda'),
            'roles_disponibles' => $this->rolesDisponibles(),
            'resumen' => [
                'total' => User::query()->count(),
                'activos' => User::query()->where('estado', User::ESTADO_ACTIVO)->count(),
                'inactivos' => User::query()->where('estado', User::ESTADO_INACTIVO)->count(),
                'administradores' => User::query()->whereHas('roles', fn ($q) => $q->where('nombre', Role::ADMIN))->count(),
                'cajeras' => User::query()->whereHas('roles', fn ($q) => $q->whereIn('nombre', [Role::CAJERA, Role::OPERADOR]))->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Usuarios/Create', [
            'roles_disponibles' => $this->rolesDisponibles(),
        ]);
    }

    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $role = $request->string('role')->toString();

        $usuario = DB::transaction(function () use ($request, $role) {
            $usuario = User::create([
                'name' => $request->string('name')->toString(),
                'email' => $request->string('email')->toString(),
                'password' => $request->string('password')->toString(),
                'email_verified_at' => now(),
                'role' => $role,
                'estado' => User::ESTADO_ACTIVO,
            ]);

            $this->createTeam->handle($usuario, $usuario->name."'s Team", isPersonal: true);
            $usuario->assignRole($role);
            $usuario->ensureRolesHavePermissions();

            return $usuario;
        });

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario): Response
    {
        $usuario->load('roles');

        return Inertia::render('Usuarios/Edit', [
            'usuario' => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'role' => $this->rolPrincipal($usuario),
                'estado' => $usuario->estado ?? User::ESTADO_ACTIVO,
            ],
            'roles_disponibles' => $this->rolesDisponibles(),
        ]);
    }

    public function update(UpdateUsuarioRequest $request, User $usuario): RedirectResponse
    {
        if ($request->user()->is($usuario) && $request->string('role')->toString() !== Role::ADMIN) {
            return back()->with('error', 'No puede quitarse el rol de administrador a usted mismo.');
        }

        $adminsCount = User::query()
            ->whereHas('roles', fn ($q) => $q->where('nombre', Role::ADMIN))
            ->whereKeyNot($usuario->id)
            ->count();

        if (
            $usuario->isAdmin()
            && $request->string('role')->toString() !== Role::ADMIN
            && $adminsCount === 0
        ) {
            return back()->with('error', 'Debe existir al menos un administrador en el sistema.');
        }

        $role = $request->string('role')->toString();
        $estado = $request->string('estado')->toString();

        if ($mensaje = $this->mensajeErrorCambioEstado($usuario, $estado, $request->user())) {
            return back()->with('error', $mensaje);
        }

        $usuario->update([
            ...$request->only(['name', 'email']),
            'estado' => $estado,
        ]);
        $usuario->roles()->sync([]);
        $usuario->assignRole($role);
        $usuario->ensureRolesHavePermissions();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function updateEstado(UpdateUsuarioEstadoRequest $request, User $usuario): RedirectResponse
    {
        $estado = $request->string('estado')->toString();

        if ($mensaje = $this->mensajeErrorCambioEstado($usuario, $estado, $request->user())) {
            return back()->with('error', $mensaje);
        }

        $usuario->update(['estado' => $estado]);

        $etiqueta = $estado === User::ESTADO_ACTIVO ? 'activado' : 'desactivado';

        return back()->with('success', "Usuario {$etiqueta} correctamente.");
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function rolesDisponibles(): array
    {
        return [
            ['value' => Role::ADMIN, 'label' => 'Administrador'],
            ['value' => Role::CAJERA, 'label' => 'Cajera'],
            ['value' => Role::OPERADOR, 'label' => 'Operador'],
        ];
    }

    private function rolPrincipal(User $user): string
    {
        if ($user->isAdmin()) {
            return Role::ADMIN;
        }

        if ($user->hasRole(Role::OPERADOR)) {
            return Role::OPERADOR;
        }

        return Role::CAJERA;
    }

    /** @deprecated Use User::etiquetaRol() */
    private function etiquetaRol(User $user): string
    {
        return $user->etiquetaRol();
    }

    private function mensajeErrorCambioEstado(User $usuario, string $estado, User $actor): ?string
    {
        if ($estado === User::ESTADO_INACTIVO && $actor->is($usuario)) {
            return 'No puede desactivar su propia cuenta.';
        }

        if ($estado === User::ESTADO_INACTIVO && $usuario->isAdmin()) {
            $adminsActivos = User::query()
                ->where('estado', User::ESTADO_ACTIVO)
                ->whereHas('roles', fn ($q) => $q->where('nombre', Role::ADMIN))
                ->whereKeyNot($usuario->id)
                ->count();

            if ($adminsActivos === 0) {
                return 'Debe existir al menos un administrador activo en el sistema.';
            }
        }

        return null;
    }

    /** @deprecated Use etiquetaRol() */
    private function roleLabel(User $user): string
    {
        return $this->etiquetaRol($user);
    }
}
