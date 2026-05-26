<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Concerns\HasTeams;
use App\Enums\Permission as PermissionEnum;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password', 'current_team_id', 'role', 'email_verified_at', 'estado'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    public const ESTADO_ACTIVO = 'activo';

    public const ESTADO_INACTIVO = 'inactivo';

    /** @use HasFactory<UserFactory> */
    use HasFactory, HasTeams, Notifiable, TwoFactorAuthenticatable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'rol_usuario', 'usuario_id', 'rol_id')->withTimestamps();
    }

    public function assignRole(string $roleName): void
    {
        $role = Role::firstOrCreate(
            ['nombre' => $roleName],
            ['descripcion' => 'Rol del sistema ISINUTA'],
        );

        $role->asegurarPermisosPorDefecto();

        $this->roles()->syncWithoutDetaching([$role->id]);
        $this->forceFill(['role' => $roleName])->save();
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->rolesNombres(), true);
    }

    /**
     * @param  array<int, string>|string  $roles
     */
    public function hasAnyRole(array|string $roles): bool
    {
        $roles = is_array($roles) ? $roles : func_get_args();

        foreach ($roles as $role) {
            if (in_array($role, $this->rolesNombres(), true)) {
                return true;
            }
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }

    public function isCajera(): bool
    {
        return $this->hasAnyRole([Role::CAJERA, Role::OPERADOR]);
    }

    /** @deprecated Use isCajera() */
    public function isOperador(): bool
    {
        return $this->isCajera();
    }

    /**
     * @return array<int, string>
     */
    public function rolesNombres(): array
    {
        $names = $this->relationLoaded('roles')
            ? $this->roles->pluck('nombre')->all()
            : $this->roles()->pluck('nombre')->all();

        $legacy = $this->legacyRoleName();

        if ($legacy && ! in_array($legacy, $names, true)) {
            $names[] = $legacy;
        }

        return $names;
    }

    /**
     * @return array<int, string>
     */
    public function permissionsNombres(): array
    {
        if ($this->isAdmin()) {
            return PermissionEnum::valuesForAdministrador();
        }

        $this->loadMissing('roles.permisos');

        $fromDatabase = $this->roles
            ->flatMap(fn (Role $role) => $role->permisos->pluck('nombre'))
            ->unique()
            ->values()
            ->all();

        if ($fromDatabase !== []) {
            return $fromDatabase;
        }

        return $this->defaultPermissionsForAssignedRoles();
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return in_array($permission, $this->permissionsNombres(), true);
    }

    public function canAccessPanel(): bool
    {
        return $this->isActivo() && $this->hasAnyRole([Role::ADMIN, Role::CAJERA, Role::OPERADOR]);
    }

    public function isActivo(): bool
    {
        return ($this->estado ?? self::ESTADO_ACTIVO) === self::ESTADO_ACTIVO;
    }

    public function etiquetaRol(): string
    {
        if ($this->isAdmin()) {
            return 'Administrador';
        }

        if ($this->hasRole(Role::OPERADOR)) {
            return 'Operador';
        }

        return 'Cajera';
    }

    public function legacyRoleName(): ?string
    {
        $raw = strtolower((string) ($this->role ?? ''));

        return match ($raw) {
            'admin', 'administrador' => Role::ADMIN,
            'cajera', 'cajero', 'operator', 'operador' => Role::CAJERA,
            default => null,
        };
    }

    public function syncRolesFromLegacyColumn(): void
    {
        $legacy = $this->legacyRoleName();

        if ($legacy) {
            $this->assignRole($legacy);
        }
    }

    public function ensureRolesHavePermissions(): void
    {
        $this->loadMissing('roles');

        foreach ($this->roles as $role) {
            $role->asegurarPermisosPorDefecto();
        }
    }

    /**
     * @return array<int, string>
     */
    protected function defaultPermissionsForAssignedRoles(): array
    {
        if ($this->isAdmin()) {
            return PermissionEnum::valuesForAdministrador();
        }

        if ($this->isCajera()) {
            return PermissionEnum::valuesForCajera();
        }

        return [];
    }
}
