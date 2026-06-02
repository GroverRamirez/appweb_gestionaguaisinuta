<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import PageHeader from '@/components/isinuta/PageHeader.vue';
import ReportNav from '@/components/isinuta/ReportNav.vue';
import StatCard from '@/components/isinuta/StatCard.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { BarChart3, TrendingUp } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    pagos: any[];
    filtros: { mes: number; anio: number };
    total: number;
    serie: Array<{ periodo: string; monto: number }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Reportes', href: '/reportes/recaudacion' },
    { title: 'Recaudación', href: '/reportes/recaudacion' },
];

const mes = ref(props.filtros.mes);
const anio = ref(props.filtros.anio);

watch([mes, anio], () => {
    router.get(
        '/reportes/recaudacion',
        { mes: mes.value, anio: anio.value },
        { preserveState: true },
    );
});

const formatBs = (n: number) =>
    new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(n);

const maxSerie = Math.max(...props.serie.map((s) => s.monto), 1);
</script>

<template>
    <Head title="Recaudación" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6">
            <PageHeader
                title="Recaudación mensual"
                description="Pagos cobrados en el período seleccionado"
                :icon="BarChart3"
            />

            <ReportNav />

            <div class="isinuta-filter-bar">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                        Mes
                    </label>
                    <input
                        v-model.number="mes"
                        type="number"
                        min="1"
                        max="12"
                        class="isinuta-select w-24"
                    />
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                        Año
                    </label>
                    <input v-model.number="anio" type="number" class="isinuta-select w-28" />
                </div>
            </div>

            <StatCard
                title="Total del período"
                :value="formatBs(total)"
                :icon="TrendingUp"
                variant="success"
            />

            <DataCard title="Pagos del período" :description="`${pagos.length} recibos`" compact>
                <div v-if="pagos.length" class="isinuta-table-wrap isinuta-table-wrap-flush">
                    <table class="isinuta-table">
                        <thead>
                            <tr>
                                <th>Recibo</th>
                                <th>Afiliado</th>
                                <th>Fecha</th>
                                <th class="text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in pagos" :key="p.id">
                                <td class="font-mono text-xs font-semibold text-primary">
                                    {{ p.numero_recibo }}
                                </td>
                                <td class="font-medium">
                                    {{ p.afiliado?.nombres }} {{ p.afiliado?.apellidos }}
                                </td>
                                <td class="text-muted-foreground">{{ p.fecha_pago }}</td>
                                <td class="text-right font-bold">Bs {{ p.total }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-else class="isinuta-empty-state text-sm text-muted-foreground">
                    No hay pagos en este período.
                </p>
            </DataCard>

            <DataCard title="Tendencia — últimos 12 meses" description="Comparativa de ingresos">
                <ul class="space-y-4">
                    <li v-for="s in serie" :key="s.periodo" class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="font-semibold">{{ s.periodo }}</span>
                            <span class="font-bold text-primary">{{ formatBs(s.monto) }}</span>
                        </div>
                        <div class="h-2.5 overflow-hidden rounded-full bg-muted/80">
                            <div
                                class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-teal-500"
                                :style="{ width: `${Math.max(4, (s.monto / maxSerie) * 100)}%` }"
                            />
                        </div>
                    </li>
                </ul>
            </DataCard>
        </div>
    </AppLayout>
</template>
