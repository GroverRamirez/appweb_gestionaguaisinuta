<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { Pencil, Plus, UserCheck, UserMinus, Users } from 'lucide-vue-next';
import { ref, watch, withDefaults } from 'vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import FlashBanner from '@/components/isinuta/FlashBanner.vue';
import PageHeader from '@/components/isinuta/PageHeader.vue';
import PaginationLinks from '@/components/isinuta/PaginationLinks.vue';
import SearchInput from '@/components/isinuta/SearchInput.vue';
import StatusBadge from '@/components/isinuta/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { usePermissions } from '@/composables/usePermissions';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const { can } = usePermissions();

type Afiliado = {
    id: number;
    ci: string;
    nombres: string;
    apellidos: string;
    telefono: string | null;
    estado: string;
};

const props = withDefaults(
    defineProps<{
    afiliados: { data: Afiliado[]; links: any[]; total: number };
    filtros: { busqueda?: string };
    resumen: { total: number; activos: number; inactivos: number };
    }>(),
    {
        filtros: () => ({}),
        resumen: () => ({ total: 0, activos: 0, inactivos: 0 }),
    },
);

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Afiliados', href: '/afiliados' }];
const busqueda = ref(props.filtros.busqueda ?? '');

const buscar = useDebounceFn((value: string) => {
    router.get(
        '/afiliados',
        { busqueda: value || undefined },
        { preserveState: true, replace: true, preserveScroll: true },
    );
}, 350);

watch(busqueda, (value) => buscar(value));

const iniciales = (afiliado: Afiliado) =>
    `${afiliado.nombres.charAt(0)}${afiliado.apellidos.charAt(0)}`.toUpperCase();

const nombreCompleto = (afiliado: Afiliado) =>
    `${afiliado.nombres} ${afiliado.apellidos}`;
</script>

<template>
    <Head title="Afiliados" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex w-full flex-1 flex-col gap-6">
            <FlashBanner />

            <PageHeader
                title="Afiliados"
                description="Registro y actualización de socios de la asociación ISINUTA"
            >
                <template v-if="can('afiliados.gestionar')" #actions>
                    <Link href="/afiliados/crear">
                        <Button class="rounded-xl shadow-md shadow-primary/20">
                            <Plus class="mr-2 size-4" />
                            Nuevo afiliado
                        </Button>
                    </Link>
                </template>
            </PageHeader>

            <div class="grid gap-3 sm:grid-cols-3">
                <div class="isinuta-card flex items-center gap-4 p-4">
                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-cyan-600 text-white shadow-lg shadow-sky-500/25"
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
                        class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25"
                    >
                        <UserCheck class="size-5" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                            Activos
                        </p>
                        <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">
                            {{ resumen.activos }}
                        </p>
                    </div>
                </div>
                <div class="isinuta-card flex items-center gap-4 p-4">
                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-slate-400 to-slate-500 text-white shadow-lg shadow-slate-500/20"
                    >
                        <UserMinus class="size-5" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                            Inactivos
                        </p>
                        <p class="text-2xl font-bold text-muted-foreground">
                            {{ resumen.inactivos }}
                        </p>
                    </div>
                </div>
            </div>

            <DataCard compact>
                <template #header>
                    <div
                        class="flex w-full flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
                    >
                        <div>
                            <h2 class="font-semibold text-foreground">Listado de afiliados</h2>
                            <p class="text-xs text-muted-foreground">
                                {{ afiliados.total }} registro(s)
                                <span v-if="filtros.busqueda"> · filtro: «{{ filtros.busqueda }}»</span>
                            </p>
                        </div>
                        <SearchInput
                            v-model="busqueda"
                            class="lg:max-w-md"
                            placeholder="Buscar por CI, nombre o apellido..."
                        />
                    </div>
                </template>

                <div class="isinuta-table-wrap -mx-1">
                    <table class="isinuta-table min-w-[640px]">
                        <thead>
                            <tr>
                                <th>Afiliado</th>
                                <th>CI</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="afiliado in afiliados.data" :key="afiliado.id">
                                <td>
                                    <div class="isinuta-table-user">
                                        <div
                                            class="isinuta-table-avatar bg-gradient-to-br from-cyan-500/15 to-teal-500/15 text-primary"
                                        >
                                            {{ iniciales(afiliado) }}
                                        </div>
                                        <p class="truncate font-semibold text-foreground">
                                            {{ nombreCompleto(afiliado) }}
                                        </p>
                                    </div>
                                </td>
                                <td class="font-mono text-xs font-medium text-muted-foreground">
                                    {{ afiliado.ci }}
                                </td>
                                <td class="text-muted-foreground">
                                    {{ afiliado.telefono || '—' }}
                                </td>
                                <td>
                                    <StatusBadge
                                        :status="afiliado.estado"
                                        :label="afiliado.estado === 'activo' ? 'Activo' : 'Inactivo'"
                                    />
                                </td>
                                <td class="text-right">
                                    <Link
                                        v-if="can('afiliados.gestionar')"
                                        :href="`/afiliados/${afiliado.id}/editar`"
                                    >
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            class="isinuta-table-btn border-primary/20 hover:border-primary/40 hover:bg-primary/5"
                                        >
                                            <Pencil class="mr-1 size-3" />
                                            Editar
                                        </Button>
                                    </Link>
                                    <span v-else class="text-xs text-muted-foreground">Solo lectura</span>
                                </td>
                            </tr>
                            <tr v-if="afiliados.data.length === 0">
                                <td colspan="5" class="py-8 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center gap-2 text-muted-foreground">
                                        <Users class="size-8 opacity-30" />
                                        <p class="font-medium">No se encontraron afiliados</p>
                                        <p class="text-xs">
                                            Pruebe otra búsqueda o registre un nuevo afiliado.
                                        </p>
                                        <Link
                                            v-if="can('afiliados.gestionar')"
                                            href="/afiliados/crear"
                                            class="mt-1"
                                        >
                                            <Button size="sm" class="isinuta-table-btn rounded-xl">
                                                <Plus class="mr-1 size-3.5" />
                                                Nuevo afiliado
                                            </Button>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="afiliados.data.length > 0" class="mt-4 border-t border-border/50 pt-3">
                    <PaginationLinks :links="afiliados.links" />
                </div>
            </DataCard>
        </div>
    </AppLayout>
</template>
