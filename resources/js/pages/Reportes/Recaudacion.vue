<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import type { BreadcrumbItem } from '@/types';

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
    router.get('/reportes/recaudacion', { mes: mes.value, anio: anio.value }, { preserveState: true });
});

const formatBs = (n: number) =>
    new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(n);
</script>

<template>
    <Head title="Recaudación" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold">Recaudación mensual</h1>
            <div class="flex gap-2 text-sm">
                <Link href="/reportes/afiliados" class="text-primary">Afiliados</Link>
                <Link href="/reportes/deudas" class="text-primary">Deudas</Link>
            </div>
            <div class="flex gap-2">
                <input v-model.number="mes" type="number" min="1" max="12" class="h-10 w-20 rounded border px-2" />
                <input v-model.number="anio" type="number" class="h-10 w-24 rounded border px-2" />
            </div>
            <p class="text-lg font-semibold">Total del período: {{ formatBs(total) }}</p>
            <div class="overflow-x-auto rounded-md border">
                <table class="min-w-full text-sm">
                    <thead class="bg-muted/50">
                        <tr>
                            <th class="px-4 py-2 text-left">Recibo</th>
                            <th class="px-4 py-2 text-left">Afiliado</th>
                            <th class="px-4 py-2 text-left">Fecha</th>
                            <th class="px-4 py-2 text-right">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in pagos" :key="p.id" class="border-t">
                            <td class="px-4 py-2">{{ p.numero_recibo }}</td>
                            <td class="px-4 py-2">
                                {{ p.afiliado?.nombres }} {{ p.afiliado?.apellidos }}
                            </td>
                            <td class="px-4 py-2">{{ p.fecha_pago }}</td>
                            <td class="px-4 py-2 text-right">Bs {{ p.total }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h2 class="font-semibold">Últimos 12 meses</h2>
            <ul class="space-y-1 text-sm">
                <li v-for="s in serie" :key="s.periodo" class="flex justify-between">
                    <span>{{ s.periodo }}</span>
                    <span>{{ formatBs(s.monto) }}</span>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
