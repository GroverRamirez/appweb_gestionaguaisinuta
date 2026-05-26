<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { usePermissions } from '@/composables/usePermissions';
import type { BreadcrumbItem } from '@/types';

const { can } = usePermissions();

defineProps<{
    tramites: { data: any[]; links: any[] };
    filtros: { estado?: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Trámites', href: '/tramites' }];
const estado = ref('');

watch(estado, (v) => {
    router.get('/tramites', { estado: v }, { preserveState: true, replace: true });
});

const aprobar = (id: number) => {
    if (confirm('¿Aprobar cambio de nombre y actualizar titular?')) {
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
        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Cambio de nombre</h1>
                <Link v-if="can('tramites.gestionar')" href="/tramites/crear">
                    <Button>Nuevo trámite</Button>
                </Link>
            </div>
            <select v-model="estado" class="h-10 w-48 rounded-md border px-2 text-sm">
                <option value="">Todos</option>
                <option value="pendiente">Pendiente</option>
                <option value="aprobado">Aprobado</option>
                <option value="rechazado">Rechazado</option>
            </select>
            <div class="overflow-x-auto rounded-md border">
                <table class="min-w-full text-sm">
                    <thead class="bg-muted/50">
                        <tr>
                            <th class="px-4 py-2 text-left">Titular actual</th>
                            <th class="px-4 py-2 text-left">Nuevo titular</th>
                            <th class="px-4 py-2 text-left">Deuda verificada</th>
                            <th class="px-4 py-2 text-left">Estado</th>
                            <th class="px-4 py-2 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="t in tramites.data" :key="t.id" class="border-t">
                            <td class="px-4 py-2">
                                {{ t.afiliado?.nombres }} {{ t.afiliado?.apellidos }}
                            </td>
                            <td class="px-4 py-2">
                                {{ t.nombres_nuevo }} {{ t.apellidos_nuevo }} ({{ t.ci_nuevo }})
                            </td>
                            <td class="px-4 py-2">
                                <span :class="t.sin_deudas_verificado ? 'text-green-600' : 'text-red-600'">
                                    Bs {{ t.deuda_total_verificada }}
                                    {{ t.sin_deudas_verificado ? '— Sin deudas' : '— Con deudas' }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ t.estado }}</td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <template v-if="t.estado === 'pendiente' && can('tramites.aprobar')">
                                    <Button size="sm" @click="aprobar(t.id)">Aprobar</Button>
                                    <Button size="sm" variant="outline" @click="rechazar(t.id)">
                                        Rechazar
                                    </Button>
                                </template>
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
        </div>
    </AppLayout>
</template>
