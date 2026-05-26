import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type AuthWithPermissions = {
    roles?: string[];
    permisos?: string[];
    permissions?: string[];
    isAdmin?: boolean;
};

export function usePermissions() {
    const page = usePage();
    const auth = computed(() => (page.props.auth ?? {}) as AuthWithPermissions);

    const roles = computed(() => auth.value.roles ?? []);
    const permisos = computed(
        () => auth.value.permisos ?? auth.value.permissions ?? [],
    );
    const isAdmin = computed(() => auth.value.isAdmin === true || roles.value.includes('admin'));

    const can = (permission: string): boolean => {
        if (isAdmin.value) {
            return true;
        }

        return permisos.value.includes(permission);
    };

    const hasRole = (...required: string[]): boolean =>
        required.some((role) => roles.value.includes(role));

    return {
        roles,
        permisos,
        permissions: permisos,
        isAdmin,
        can,
        hasRole,
    };
}
