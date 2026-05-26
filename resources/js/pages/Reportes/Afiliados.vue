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
        <div class="flex flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold">Afiliados</h1>
            <p class="text-sm">
                Activos: {{ resumen.activos }} | Inactivos: {{ resumen.inactivos }} | Total:
                {{ resumen.total }}
            </p>
            <div class="flex gap-2 text-sm">
                <Link href="/reportes/recaudacion" class="text-primary">Recaudación</Link>
                <Link href="/reportes/deudas" class="text-primary">Deudas</Link>
            </div>
            <div class="overflow-x-auto rounded-md border">
                <table class="min-w-full text-sm">
                    <thead class="bg-muted/50">
                        <tr>
                            <th class="px-4 py-2 text-left">CI</th>
                            <th class="px-4 py-2 text-left">Nombre</th>
                            <th class="px-4 py-2 text-left">Teléfono</th>
                            <th class="px-4 py-2 text-left">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="a in afiliados" :key="a.id" class="border-t">
                            <td class="px-4 py-2">{{ a.ci }}</td>
                            <td class="px-4 py-2">{{ a.nombres }} {{ a.apellidos }}</td>
                            <td class="px-4 py-2">{{ a.telefono ?? '—' }}</td>
                            <td class="px-4 py-2">{{ a.estado }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
