export type User = {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
};

export type Auth = {
    user: User | null;
    roles?: string[];
    permisos?: string[];
    etiqueta_rol?: string | null;
    isAdmin?: boolean;
    requires_two_factor_setup?: boolean;
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};
