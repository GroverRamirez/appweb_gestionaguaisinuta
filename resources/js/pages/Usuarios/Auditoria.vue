<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import FlashBanner from '@/components/isinuta/FlashBanner.vue';
import PageHeader from '@/components/isinuta/PageHeader.vue';
import PaginationLinks from '@/components/isinuta/PaginationLinks.vue';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, ClipboardList } from 'lucide-vue-next';
import { ref, watch } from 'vue';

type Actor = { id: number; name: string; email: string };
type Registro = {
    id: number;
    accion: string;
    datos: Record<string, unknown> | null;
    ip: string | null;
    created_at: string;
    actor: Actor | null;
    usuario_afectado: Actor | null;
};

type AccionOption = { value: string; label: string };

const props = defineProps<{
    registros: { data: Registro[]; links: unknown[]; total: number };
    filtros: { accion?: string };
    acciones: AccionOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Gestión de usuarios', href: '/usuarios' },
    { title: 'Auditoría', href: '/usuarios/auditoria' },
];

const accion = ref(props.filtros.accion ?? '');

watch(accion, (value) => {
    router.get(
        '/usuarios/auditoria',
        { accion: value || undefined },
        { preserveState: true, replace: true, preserveScroll: true },
    );
});

const etiquetaAccion = (valor: string) =>
    props.acciones.find((item) => item.value === valor)?.label ?? valor;

const formatearFecha = (iso: string) =>
    new Date(iso).toLocaleString('es-BO', {
        dateStyle: 'short',
        timeStyle: 'short',
    });
</script>

<template>
    <Head title="Auditoría de usuarios" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex w-full flex-1 flex-col gap-6">
            <FlashBanner />

            <PageHeader
                title="Auditoría de usuarios"
                description="Historial de altas, cambios y bajas en cuentas del sistema"
            >
                <template #actions>
                    <Link href="/usuarios">
                        <Button variant="outline" class="rounded-xl">
                            <ArrowLeft class="mr-1.5 size-4" />
                            Volver a usuarios
                        </Button>
                    </Link>
                </template>
            </PageHeader>

            <DataCard title="Registros" :description="`${registros.total} evento(s)`" compact>
                <div class="mb-4 flex flex-wrap items-end gap-3">
                    <div class="flex flex-col gap-1">
                        <label for="filtro-accion" class="text-xs font-medium text-muted-foreground">
                            Acción
                        </label>
                        <select
                            id="filtro-accion"
                            v-model="accion"
                            class="h-9 min-w-[220px] rounded-lg border border-input bg-background px-3 text-sm"
                        >
                            <option value="">Todas</option>
                            <option
                                v-for="item in acciones"
                                :key="item.value"
                                :value="item.value"
                            >
                                {{ item.label }}
                            </option>
                        </select>
                    </div>
                </div>

                <div v-if="registros.data.length" class="isinuta-table-wrap isinuta-table-wrap-flush">
                    <table class="isinuta-table w-full text-sm">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Acción</th>
                                <th>Actor</th>
                                <th>Usuario afectado</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="registro in registros.data" :key="registro.id">
                                <td class="whitespace-nowrap text-muted-foreground">
                                    {{ formatearFecha(registro.created_at) }}
                                </td>
                                <td>{{ etiquetaAccion(registro.accion) }}</td>
                                <td>
                                    <span v-if="registro.actor">
                                        {{ registro.actor.name }}
                                        <span class="block text-xs text-muted-foreground">
                                            {{ registro.actor.email }}
                                        </span>
                                    </span>
                                    <span v-else class="text-muted-foreground">—</span>
                                </td>
                                <td>
                                    <span v-if="registro.usuario_afectado">
                                        {{ registro.usuario_afectado.name }}
                                        <span class="block text-xs text-muted-foreground">
                                            {{ registro.usuario_afectado.email }}
                                        </span>
                                    </span>
                                    <span v-else class="text-muted-foreground">—</span>
                                </td>
                                <td class="font-mono text-xs text-muted-foreground">
                                    {{ registro.ip ?? '—' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-else
                    class="flex flex-col items-center justify-center gap-2 py-12 text-center text-muted-foreground"
                >
                    <ClipboardList class="size-10 opacity-40" />
                    <p>No hay registros de auditoría con los filtros actuales.</p>
                </div>

                <div v-if="registros.data.length" class="mt-4 border-t border-border/50 pt-3">
                    <PaginationLinks :links="registros.links" />
                </div>
            </DataCard>
        </div>
    </AppLayout>
</template>
