<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import PageHeader from '@/components/isinuta/PageHeader.vue';
import ReportNav from '@/components/isinuta/ReportNav.vue';
import StatCard from '@/components/isinuta/StatCard.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { AlertTriangle, BarChart3, CreditCard } from 'lucide-vue-next';

defineProps<{
    afiliados: Array<{
        id: number;
        ci: string;
        nombres: string;
        apellidos: string;
        pagos_pendientes: number;
        deuda_pagos: number;
        deuda_multas: number;
        deuda_total: number;
    }>;
    total_deuda: number;
    multas_pendientes: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Reportes', href: '/reportes/recaudacion' },
    { title: 'Deudas', href: '/reportes/deudas' },
];

const formatBs = (n: number) =>
    new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(n);
</script>

<template>
    <Head title="Deudas pendientes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6">
            <PageHeader
                title="Deudas pendientes"
                description="Afiliados con cuotas o multas sin cancelar"
                :icon="BarChart3"
            />

            <ReportNav />

            <div class="grid gap-4 sm:grid-cols-2">
                <StatCard
                    title="Deuda en pagos"
                    :value="formatBs(total_deuda)"
                    :icon="CreditCard"
                    variant="warning"
                />
                <StatCard
                    title="Multas pendientes"
                    :value="formatBs(multas_pendientes)"
                    :icon="AlertTriangle"
                    variant="danger"
                />
            </div>

            <DataCard
                title="Detalle por afiliado"
                :description="`${afiliados.length} afiliados con deuda`"
                compact
            >
                <div v-if="afiliados.length" class="isinuta-table-wrap isinuta-table-wrap-flush">
                    <table class="isinuta-table">
                        <thead>
                            <tr>
                                <th>Afiliado</th>
                                <th>Meses pend.</th>
                                <th class="text-right">Pagos</th>
                                <th class="text-right">Multas</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="a in afiliados" :key="a.id">
                                <td>
                                    <span class="font-medium">
                                        {{ a.nombres }} {{ a.apellidos }}
                                    </span>
                                    <span class="block text-xs text-muted-foreground">
                                        CI {{ a.ci }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="inline-flex size-8 items-center justify-center rounded-lg bg-amber-500/15 text-sm font-bold text-amber-700 dark:text-amber-300"
                                    >
                                        {{ a.pagos_pendientes }}
                                    </span>
                                </td>
                                <td class="text-right">{{ formatBs(a.deuda_pagos) }}</td>
                                <td class="text-right">{{ formatBs(a.deuda_multas) }}</td>
                                <td class="text-right font-bold text-red-600 dark:text-red-400">
                                    {{ formatBs(a.deuda_total) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="isinuta-empty-state">
                    <CreditCard class="mb-3 size-10 text-emerald-500/50" />
                    <p class="font-semibold text-foreground">¡Excelente!</p>
                    <p class="text-sm text-muted-foreground">No hay deudas pendientes registradas.</p>
                </div>
            </DataCard>
        </div>
    </AppLayout>
</template>
