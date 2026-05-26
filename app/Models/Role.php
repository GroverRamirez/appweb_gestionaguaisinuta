<?php

namespace App\Models;

use App\Enums\Permission as PermissionEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    public const ADMIN = 'admin';

    public const CAJERA = 'cajera';

    /** @deprecated Use CAJERA; se mantiene por compatibilidad. */
    public const OPERADOR = 'operador';

    protected $table = 'roles';

    protected $fillable = ['nombre', 'descripcion'];

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'rol_usuario', 'rol_id', 'usuario_id')->withTimestamps();
    }

    /** @deprecated Use usuarios() */
    public function users(): BelongsToMany
    {
        return $this->usuarios();
    }

    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(Permiso::class, 'permiso_rol', 'rol_id', 'permiso_id')->withTimestamps();
    }

    /**
     * @param  array<int, string>  $nombresPermisos
     */
    public function sincronizarPermisosPorNombre(array $nombresPermisos): void
    {
        $ids = Permiso::query()
            ->whereIn('nombre', $nombresPermisos)
            ->pluck('id');

        $this->permisos()->sync($ids);
    }

    /** @deprecated Use sincronizarPermisosPorNombre() */
    public function syncPermissionsByName(array $permissionNames): void
    {
        $this->sincronizarPermisosPorNombre($permissionNames);
    }

    public function sincronizarPermisosDesdeEnum(array $permisos): void
    {
        $nombres = array_map(
            fn (PermissionEnum|string $permiso) => $permiso instanceof PermissionEnum
                ? $permiso->value
                : $permiso,
            $permisos,
        );

        $this->sincronizarPermisosPorNombre($nombres);
    }

    /** @deprecated Use sincronizarPermisosDesdeEnum() */
    public function syncPermissionsFromEnum(array $permissions): void
    {
        $this->sincronizarPermisosDesdeEnum($permissions);
    }

    public function asegurarPermisosPorDefecto(): void
    {
        if ($this->permisos()->exists()) {
            return;
        }

        $permisos = match ($this->nombre) {
            self::ADMIN => PermissionEnum::forAdministrador(),
            self::CAJERA, self::OPERADOR => PermissionEnum::forCajera(),
            default => [],
        };

        if ($permisos !== []) {
            $this->sincronizarPermisosDesdeEnum($permisos);
        }
    }

    /** @deprecated Use asegurarPermisosPorDefecto() */
    public function ensureDefaultPermissions(): void
    {
        $this->asegurarPermisosPorDefecto();
    }
}
