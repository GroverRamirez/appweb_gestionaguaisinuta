<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import FlashBanner from '@/components/isinuta/FlashBanner.vue';
import PageHeader from '@/components/isinuta/PageHeader.vue';
import PaginationLinks from '@/components/isinuta/PaginationLinks.vue';
import SearchInput from '@/components/isinuta/SearchInput.vue';
import StatusBadge from '@/components/isinuta/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { Pencil, Plus, Shield, UserCheck, UserX, Users } from 'lucide-vue-next';
import { computed, ref, watch, withDefaults } from 'vue';

type Usuario = {
    id: number;
    name: string;
    email: string;
    roles: string[];
    etiqueta_rol: string;
    estado: string;
    email_verified_at: string | null;
};

const props = withDefaults(
    defineProps<{
    usuarios: { data: Usuario[]; links: any[]; total: number };
    filtros: { busqueda?: string };
    resumen: {
        total: number;
        activos: number;
        inactivos: number;
        administradores: number;
        cajeras: number;
    };
    }>(),
    {
        filtros: () => ({}),
        resumen: () => ({ total: 0, activos: 0, inactivos: 0, administradores: 0, cajeras: 0 }),
    },
);

const page = usePage();
const usuarioActualId = computed(() => (page.props.auth as { user?: { id: number } }).user?.id);

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Gestión de usuarios', href: '/usuarios' }];
const busqueda = ref(props.filtros.busqueda ?? '');

const buscar = useDebounceFn((value: string) => {
    router.get(
        '/usuarios',
        { busqueda: value || undefined },
        { preserveState: true, replace: true, preserveScroll: true },
    );
}, 350);

watch(busqueda, (value) => buscar(value));

const iniciales = (usuario: Usuario) =>
    usuario.name
        .split(' ')
        .map((p) => p.charAt(0))
        .join('')
        .slice(0, 2)
        .toUpperCase();

const clasesRol = (rol: string) => {
    if (rol === 'Administrador') {
        return 'bg-emerald-500/15 text-emerald-700 ring-emerald-500/20 dark:text-emerald-300';
    }

    if (rol === 'Operador') {
        return 'bg-orange-500/15 text-orange-800 ring-orange-500/20 dark:text-orange-300';
    }

    return 'bg-amber-500/15 text-amber-800 ring-amber-500/20 dark:text-amber-300';
};

const cambiarEstado = (usuario: Usuario) => {
    const estado = usuario.estado === 'activo' ? 'inactivo' : 'activo';

    router.patch(
        `/usuarios/${usuario.id}/estado`,
        { estado },
        { preserveScroll: true, preserveState: true },
    );
};
</script>

<template>
    <Head title="Gestión de usuarios" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex w-full flex-1 flex-col gap-6">
            <FlashBanner />

            <PageHeader
                title="Gestión de usuarios"
                description="Administre cuentas, roles y accesos al sistema ISINUTA"
            >
                <template #actions>
                    <Link href="/usuarios/crear">
                        <Button class="rounded-xl shadow-md shadow-primary/20">
                            <Plus class="mr-1.5 size-4" />
                            Nuevo usuario
                        </Button>
                    </Link>
                </template>
            </PageHeader>

            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="isinuta-card flex items-center gap-4 p-4">
                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 text-white shadow-lg shadow-violet-500/25"
                    >
                        <Users class="size-5" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                            Total
                        </p>
                        <p class="text-2xl font-bold">{{ resumen.total }}</p>
                    </div>
                </div>
                <div class="isinuta-card flex items-center gap-4 p-4">
                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25"
                    >
                        <UserCheck class="size-5" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                            Activos
                        </p>
                        <p class="text-2xl font-bold">{{ resumen.activos }}</p>
                    </div>
                </div>
                <div class="isinuta-card flex items-center gap-4 p-4">
                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-red-600 text-white shadow-lg shadow-rose-500/25"
                    >
                        <UserX class="size-5" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                            Inactivos
                        </p>
                        <p class="text-2xl font-bold">{{ resumen.inactivos }}</p>
                    </div>
                </div>
                <div class="isinuta-card flex items-center gap-4 p-4">
                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-500/25"
                    >
                        <Shield class="size-5" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                            Administradores
                        </p>
                        <p class="text-2xl font-bold">{{ resumen.administradores }}</p>
                    </div>
                </div>
            </div>

            <DataCard>
                <template #header>
                    <div
                        class="flex w-full flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
                    >
                        <div>
                            <h2 class="font-semibold text-foreground">Cuentas registradas</h2>
                            <p class="text-xs text-muted-foreground">
                                {{ usuarios.total }} usuario(s)
                            </p>
                        </div>
                        <SearchInput
                            v-model="busqueda"
                            class="lg:max-w-md"
                            placeholder="Buscar por nombre o correo..."
                        />
                    </div>
                </template>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-sm">
                        <thead>
                            <tr
                                class="border-b border-border/80 text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground"
                            >
                                <th class="pb-3 pr-4">Usuario</th>
                                <th class="pb-3 pr-4">Correo</th>
                                <th class="pb-3 pr-4">Rol</th>
                                <th class="pb-3 pr-4">Estado</th>
                                <th class="pb-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/50">
                            <tr
                                v-for="usuario in usuarios.data"
                                :key="usuario.id"
                                class="transition-colors hover:bg-primary/5"
                            >
                                <td class="py-4 pr-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500/15 to-purple-500/15 text-sm font-bold text-primary ring-1 ring-primary/10"
                                        >
                                            {{ iniciales(usuario) }}
                                        </div>
                                        <p class="font-semibold">{{ usuario.name }}</p>
                                    </div>
                                </td>
                                <td class="py-4 pr-4 text-muted-foreground">
                                    {{ usuario.email }}
                                </td>
                                <td class="py-4 pr-4">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset"
                                        :class="clasesRol(usuario.etiqueta_rol)"
                                    >
                                        {{ usuario.etiqueta_rol }}
                                    </span>
                                </td>
                                <td class="py-4 pr-4">
                                    <StatusBadge
                                        :status="usuario.estado"
                                        :label="usuario.estado === 'activo' ? 'Activo' : 'Inactivo'"
                                    />
                                </td>
                                <td class="py-4 text-right">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            class="rounded-lg"
                                            :disabled="usuario.id === usuarioActualId && usuario.estado === 'activo'"
                                            @click="cambiarEstado(usuario)"
                                        >
                                            {{ usuario.estado === 'activo' ? 'Desactivar' : 'Activar' }}
                                        </Button>
                                        <Link :href="`/usuarios/${usuario.id}/editar`">
                                            <Button variant="outline" size="sm" class="rounded-lg">
                                                <Pencil class="mr-1.5 size-3.5" />
                                                Editar
                                            </Button>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="usuarios.data.length === 0">
                                <td colspan="5" class="py-12 text-center text-muted-foreground">
                                    No se encontraron usuarios.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="usuarios.data.length > 0" class="mt-6 border-t border-border/50 pt-4">
                    <PaginationLinks :links="usuarios.links" />
                </div>
            </DataCard>
        </div>
    </AppLayout>
</template>
