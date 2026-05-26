export const etiquetaEstado: Record<string, string> = {
    activo: 'Activo',
    inactivo: 'Inactivo',
    pendiente: 'Pendiente',
    pagado: 'Pagado',
    pagada: 'Pagada',
    aprobado: 'Aprobado',
    rechazado: 'Rechazado',
};

export const etiquetaMetodoPago: Record<string, string> = {
    efectivo: 'Efectivo',
    transferencia: 'Transferencia',
    qr: 'QR',
    otro: 'Otro',
};

export const etiquetaTipoMulta: Record<string, string> = {
    trimestral: 'Trimestral',
    anual: 'Anual',
    otro: 'Otro',
};

export function etiquetaDe(clave: string, mapa: Record<string, string>): string {
    return mapa[clave.toLowerCase()] ?? clave;
}
