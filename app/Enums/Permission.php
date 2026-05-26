<?php

namespace App\Enums;

enum Permission: string
{
    case PanelVer = 'panel.ver';
    case AfiliadosVer = 'afiliados.ver';
    case AfiliadosGestionar = 'afiliados.gestionar';
    case PagosVer = 'pagos.ver';
    case PagosGestionar = 'pagos.gestionar';
    case MultasVer = 'multas.ver';
    case MultasGestionar = 'multas.gestionar';
    case TramitesVer = 'tramites.ver';
    case TramitesGestionar = 'tramites.gestionar';
    case TramitesAprobar = 'tramites.aprobar';
    case ReportesVer = 'reportes.ver';
    case ReportesDeudas = 'reportes.deudas';
    case UsuariosGestionar = 'usuarios.gestionar';

    public function label(): string
    {
        return match ($this) {
            self::PanelVer => 'Ver panel',
            self::AfiliadosVer => 'Ver afiliados',
            self::AfiliadosGestionar => 'Gestionar afiliados',
            self::PagosVer => 'Ver pagos',
            self::PagosGestionar => 'Registrar y cobrar pagos',
            self::MultasVer => 'Ver multas',
            self::MultasGestionar => 'Registrar multas y cobros',
            self::TramitesVer => 'Ver trámites',
            self::TramitesGestionar => 'Registrar trámites',
            self::TramitesAprobar => 'Aprobar o rechazar trámites',
            self::ReportesVer => 'Ver reportes de recaudación',
            self::ReportesDeudas => 'Ver reporte de deudas',
            self::UsuariosGestionar => 'Gestionar usuarios del sistema',
        };
    }

    public function group(): string
    {
        return match ($this) {
            self::PanelVer => 'panel',
            self::AfiliadosVer, self::AfiliadosGestionar => 'afiliados',
            self::PagosVer, self::PagosGestionar => 'pagos',
            self::MultasVer, self::MultasGestionar => 'multas',
            self::TramitesVer, self::TramitesGestionar, self::TramitesAprobar => 'tramites',
            self::ReportesVer, self::ReportesDeudas => 'reportes',
            self::UsuariosGestionar => 'usuarios',
        };
    }

    /**
     * @return array<int, self>
     */
    public static function forAdministrador(): array
    {
        return self::cases();
    }

    /**
     * Permisos de la cajera: operación diaria sin administración completa.
     *
     * @return array<int, self>
     */
    public static function forCajera(): array
    {
        return [
            self::PanelVer,
            self::AfiliadosVer,
            self::PagosVer,
            self::PagosGestionar,
            self::MultasVer,
            self::MultasGestionar,
            self::TramitesVer,
            self::TramitesGestionar,
            self::ReportesVer,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function valuesForCajera(): array
    {
        return array_map(
            fn (self $permission) => $permission->value,
            self::forCajera(),
        );
    }

    /**
     * @return array<int, string>
     */
    public static function valuesForAdministrador(): array
    {
        return array_map(
            fn (self $permission) => $permission->value,
            self::forAdministrador(),
        );
    }
}
