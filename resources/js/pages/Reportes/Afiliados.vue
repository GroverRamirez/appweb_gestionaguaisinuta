<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import PageHeader from '@/components/isinuta/PageHeader.vue';
import ReportNav from '@/components/isinuta/ReportNav.vue';
import StatCard from '@/components/isinuta/StatCard.vue';
import StatusBadge from '@/components/isinuta/StatusBadge.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { BarChart3, UserCheck, UserMinus, Users } from 'lucide-vue-next';

defineProps<{
    afiliados: Array<{
        id: number;
        ci: string;
        nombres: string;
        apellidos: string;
        telefono?: string;
        estado: string;
        fecha_afiliacion?: string;
    }>;
    resumen: { activos: number; inactivos: number; total: number };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Reportes', href: '/reportes/recaudacion' },
    { title: 'Afiliados', href: '/reportes/afiliados' },
];
</script>

<template>
    <Head title="Reporte de afiliados" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6">
            <PageHeader
                title="Listado de afiliados"
                description="Reporte consolidado de socios de la asociación"
                :icon="BarChart3"
            />

            <ReportNav />

            <div class="grid gap-4 sm:grid-cols-3">
                <StatCard title="Total" :value="resumen.total" :icon="Users" variant="info" />
                <StatCard
                    title="Activos"
                    :value="resumen.activos"
                    :icon="UserCheck"
                    variant="success"
                />
                <StatCard
                    title="Inactivos"
                    :value="resumen.inactivos"
                    :icon="UserMinus"
                    variant="default"
                />
            </div>

            <DataCard title="Detalle" :description="`${afiliados.length} registros`" compact>
                <div v-if="afiliados.length" class="isinuta-table-wrap isinuta-table-wrap-flush">
                    <table class="isinuta-table">
                        <thead>
                            <tr>
                                <th>CI</th>
                                <th>Nombre completo</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="a in afiliados" :key="a.id">
                                <td class="font-mono text-xs font-semibold">{{ a.ci }}</td>
                                <td class="font-medium">{{ a.nombres }} {{ a.apellidos }}</td>
                                <td class="text-muted-foreground">{{ a.telefono ?? '—' }}</td>
                                <td><StatusBadge :status="a.estado" /></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-else class="isinuta-empty-state text-sm text-muted-foreground">
                    No hay afiliados registrados.
                </p>
            </DataCard>
        </div>
    </AppLayout>
</template>
