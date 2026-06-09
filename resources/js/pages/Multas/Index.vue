<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { AlertTriangle, Plus } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import FilterSelect from '@/components/isinuta/FilterSelect.vue';
import FlashBanner from '@/components/isinuta/FlashBanner.vue';
import PageHeader from '@/components/isinuta/PageHeader.vue';
import SearchInput from '@/components/isinuta/SearchInput.vue';
import StatusBadge from '@/components/isinuta/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { etiquetaDe, etiquetaTipoMulta } from '@/lib/etiquetas';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    multas: { data: any[]; links: any[] };
    filtros: { q?: string; estado?: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Multas', href: '/multas' }];
const q = ref(props.filtros.q ?? '');
const estado = ref(props.filtros.estado ?? '');

const buscar = useDebounceFn(() => {
    router.get(
        '/multas',
        { q: q.value || undefined, estado: estado.value || undefined },
        { preserveState: true, replace: true },
    );
}, 350);

watch([q, estado], () => buscar());

const marcarPagada = (id: number) => router.post(`/multas/${id}/pagar`);
</script>

<template>
    <Head title="Multas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6">
            <FlashBanner />

            <PageHeader
                title="Multas por mora"
                description="Bs 50 trimestral · Bs 150 anual por meses de atraso"
                :icon="AlertTriangle"
            >
                <template #actions>
                    <Link href="/multas/crear">
                        <Button class="rounded-xl shadow-md shadow-primary/20">
                            <Plus class="mr-2 size-4" />
                            Nueva multa
                        </Button>
                    </Link>
                </template>
            </PageHeader>

            <div class="isinuta-filter-bar">
                <SearchInput v-model="q" placeholder="Buscar afiliado..." class="max-w-sm flex-1" />
                <FilterSelect v-model="estado" label="Estado">
                    <option value="">Todas</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="pagada">Pagada</option>
                </FilterSelect>
            </div>

            <DataCard title="Registro de multas" compact>
                <div v-if="multas.data.length" class="isinuta-table-wrap isinuta-table-wrap-flush">
                    <table class="isinuta-table">
                        <thead>
                            <tr>
                                <th>Afiliado</th>
                                <th>Tipo</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th class="text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="m in multas.data" :key="m.id">
                                <td class="font-medium">
                                    {{ m.afiliado?.nombres }} {{ m.afiliado?.apellidos }}
                                </td>
                                <td class="text-muted-foreground">
                                    {{ etiquetaDe(m.tipo, etiquetaTipoMulta) }} ({{ m.meses_mora }} m.)
                                </td>
                                <td class="font-bold">Bs {{ m.monto }}</td>
                                <td><StatusBadge :status="m.estado" /></td>
                                <td class="text-right">
                                    <Button
                                        v-if="m.estado === 'pendiente'"
                                        size="sm"
                                        variant="outline"
                                        class="isinuta-table-btn"
                                        @click="marcarPagada(m.id)"
                                    >
                                        Marcar pagada
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="isinuta-empty-state">
                    <AlertTriangle class="mb-3 size-10 text-muted-foreground/40" />
                    <p class="text-sm text-muted-foreground">No hay multas con los filtros actuales.</p>
                </div>
            </DataCard>
        </div>
    </AppLayout>
</template>
