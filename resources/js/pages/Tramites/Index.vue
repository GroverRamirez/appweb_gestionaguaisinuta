<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import FilterSelect from '@/components/isinuta/FilterSelect.vue';
import FlashBanner from '@/components/isinuta/FlashBanner.vue';
import PageHeader from '@/components/isinuta/PageHeader.vue';
import StatusBadge from '@/components/isinuta/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { usePermissions } from '@/composables/usePermissions';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Check, ClipboardList, Plus, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const { can } = usePermissions();

const props = defineProps<{
    tramites: { data: any[]; links: any[] };
    filtros: { estado?: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Trámites', href: '/tramites' }];
const estado = ref(props.filtros.estado ?? '');

watch(estado, (v) => {
    router.get('/tramites', { estado: v || undefined }, { preserveState: true, replace: true });
});

const aprobar = (id: number) => {
    if (confirm('¿Aprobar cambio de titular y actualizar datos del afiliado?')) {
        router.post(`/tramites/${id}/aprobar`);
    }
};

const rechazar = (id: number) => {
    const obs = prompt('Motivo del rechazo (opcional):');
    router.post(`/tramites/${id}/rechazar`, { observaciones: obs ?? '' });
};
</script>

<template>
    <Head title="Trámites" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6">
            <FlashBanner />

            <PageHeader
                title="Cambio de titular"
                description="Trámites de cambio de nombre con verificación de deudas pendientes"
                :icon="ClipboardList"
            >
                <template v-if="can('tramites.gestionar')" #actions>
                    <Link href="/tramites/crear">
                        <Button class="rounded-xl shadow-md shadow-primary/20">
                            <Plus class="mr-2 size-4" />
                            Nuevo trámite
                        </Button>
                    </Link>
                </template>
            </PageHeader>

            <div class="isinuta-filter-bar">
                <FilterSelect v-model="estado" label="Estado">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="aprobado">Aprobado</option>
                    <option value="rechazado">Rechazado</option>
                </FilterSelect>
            </div>

            <DataCard title="Listado de trámites" :description="`${tramites.data.length} registros`" compact>
                <div v-if="tramites.data.length" class="isinuta-table-wrap isinuta-table-wrap-flush">
                    <table class="isinuta-table">
                        <thead>
                            <tr>
                                <th>Titular actual</th>
                                <th>Nuevo titular</th>
                                <th>Deuda al registrar</th>
                                <th>Estado</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="t in tramites.data" :key="t.id">
                                <td class="font-medium">
                                    {{ t.afiliado?.nombres }} {{ t.afiliado?.apellidos }}
                                </td>
                                <td>
                                    <span class="font-medium">
                                        {{ t.nombres_nuevo }} {{ t.apellidos_nuevo }}
                                    </span>
                                    <span class="block text-xs text-muted-foreground">
                                        CI {{ t.ci_nuevo }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        :class="
                                            t.sin_deudas_verificado
                                                ? 'font-semibold text-emerald-600 dark:text-emerald-400'
                                                : 'font-semibold text-red-600 dark:text-red-400'
                                        "
                                    >
                                        Bs {{ t.deuda_total_verificada }}
                                    </span>
                                    <span class="block text-xs text-muted-foreground">
                                        {{
                                            t.sin_deudas_verificado
                                                ? 'Sin deudas'
                                                : 'Con deudas'
                                        }}
                                    </span>
                                </td>
                                <td>
                                    <StatusBadge :status="t.estado" />
                                </td>
                                <td class="text-right">
                                    <div
                                        v-if="t.estado === 'pendiente' && can('tramites.aprobar')"
                                        class="isinuta-table-actions"
                                    >
                                        <Button size="sm" class="isinuta-table-btn" @click="aprobar(t.id)">
                                            <Check class="mr-1 size-3" />
                                            Aprobar
                                        </Button>
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            class="isinuta-table-btn"
                                            @click="rechazar(t.id)"
                                        >
                                            <X class="mr-1 size-3" />
                                            Rechazar
                                        </Button>
                                    </div>
                                    <span
                                        v-else-if="t.estado === 'pendiente'"
                                        class="text-xs text-muted-foreground"
                                    >
                                        Pendiente de administrador
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="isinuta-empty-state">
                    <ClipboardList class="mb-3 size-10 text-muted-foreground/40" />
                    <p class="text-sm text-muted-foreground">No hay trámites con los filtros actuales.</p>
                </div>
            </DataCard>
        </div>
    </AppLayout>
</template>
