<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    BarChart3,
    ClipboardList,
    CreditCard,
    Droplets,
    LayoutGrid,
    UserCog,
    Users,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { usePermissions } from '@/composables/usePermissions';
import type { NavItem } from '@/types';

const { can, hasRole } = usePermissions();

const puedeUsarPanel = computed(
    () => hasRole('admin', 'cajera', 'operador') && can('panel.ver'),
);

type NavItemWithPermission = NavItem & { permission?: string };

const allNavItems: NavItemWithPermission[] = [
    { title: 'Panel', href: '/panel', icon: LayoutGrid, permission: 'panel.ver' },
    { title: 'Afiliados', href: '/afiliados', icon: Users, permission: 'afiliados.ver' },
    { title: 'Pagos', href: '/pagos', icon: CreditCard, permission: 'pagos.ver' },
    { title: 'Multas', href: '/multas', icon: AlertTriangle, permission: 'multas.ver' },
    { title: 'Trámites', href: '/tramites', icon: ClipboardList, permission: 'tramites.ver' },
    {
        title: 'Reportes',
        href: '/reportes/recaudacion',
        icon: BarChart3,
        permission: 'reportes.ver',
        matchPrefix: true,
    },
    {
        title: 'Gestión de usuarios',
        href: '/usuarios',
        icon: UserCog,
        permission: 'usuarios.gestionar',
    },
];

const mainNavItems = computed<NavItem[]>(() => {
    if (!puedeUsarPanel.value) {
        return [];
    }

    return allNavItems.filter((item) => !item.permission || can(item.permission));
});

const footerNavItems: NavItem[] = [
    {
        title: 'ISINUTA - Agua Potable',
        href: '/',
        icon: Droplets,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="sidebar" class="isinuta-sidebar border-0 shadow-none">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/panel">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
