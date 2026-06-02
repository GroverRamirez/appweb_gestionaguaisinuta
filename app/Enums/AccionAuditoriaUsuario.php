<?php

namespace App\Enums;

enum AccionAuditoriaUsuario: string
{
    case Creado = 'creado';
    case Actualizado = 'actualizado';
    case EstadoCambiado = 'estado_cambiado';
    case Eliminado = 'eliminado';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Creado => 'Usuario creado',
            self::Actualizado => 'Usuario actualizado',
            self::EstadoCambiado => 'Estado cambiado',
            self::Eliminado => 'Usuario eliminado',
        };
    }
}
