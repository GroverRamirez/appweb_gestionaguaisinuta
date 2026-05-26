<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';

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
        <div class="flex flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold">Deudas pendientes</h1>
            <p class="text-sm">
                Total deuda pagos: {{ formatBs(total_deuda) }} | Multas pendientes:
                {{ formatBs(multas_pendientes) }}
            </p>
            <div class="flex gap-2 text-sm">
                <Link href="/reportes/afiliados" class="text-primary">Afiliados</Link>
                <Link href="/reportes/recaudacion" class="text-primary">Recaudación</Link>
            </div>
            <div class="overflow-x-auto rounded-md border">
                <table class="min-w-full text-sm">
                    <thead class="bg-muted/50">
                        <tr>
                            <th class="px-4 py-2 text-left">Afiliado</th>
                            <th class="px-4 py-2 text-left">Meses pend.</th>
                            <th class="px-4 py-2 text-right">Pagos</th>
                            <th class="px-4 py-2 text-right">Multas</th>
                            <th class="px-4 py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="a in afiliados" :key="a.id" class="border-t">
                            <td class="px-4 py-2">{{ a.nombres }} {{ a.apellidos }} ({{ a.ci }})</td>
                            <td class="px-4 py-2">{{ a.pagos_pendientes }}</td>
                            <td class="px-4 py-2 text-right">{{ formatBs(a.deuda_pagos) }}</td>
                            <td class="px-4 py-2 text-right">{{ formatBs(a.deuda_multas) }}</td>
                            <td class="px-4 py-2 text-right font-medium">
                                {{ formatBs(a.deuda_total) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
