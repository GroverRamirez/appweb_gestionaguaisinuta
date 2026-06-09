<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { CalendarPlus, CreditCard, Plus, Receipt } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import FilterSelect from '@/components/isinuta/FilterSelect.vue';
import FlashBanner from '@/components/isinuta/FlashBanner.vue';
import PageHeader from '@/components/isinuta/PageHeader.vue';
import SearchInput from '@/components/isinuta/SearchInput.vue';
import StatusBadge from '@/components/isinuta/StatusBadge.vue';
import TarifaBanner from '@/components/isinuta/TarifaBanner.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    pagos: { data: any[]; links: any[] };
    filtros: { q?: string; estado?: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Pagos', href: '/pagos' }];
const q = ref(props.filtros.q ?? '');
const estado = ref(props.filtros.estado ?? '');

const buscar = useDebounceFn(() => {
    router.get(
        '/pagos',
        { q: q.value || undefined, estado: estado.value || undefined },
        { preserveState: true, replace: true },
    );
}, 350);

watch([q, estado], () => buscar());

const mesNombre = (mes: number) =>
    new Date(2000, mes - 1, 1).toLocaleString('es-BO', { month: 'long' });
</script>

<template>
    <Head title="Pagos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6">
            <FlashBanner />

            <PageHeader
                title="Control de pagos"
                description="Registro y cobro de cuotas mensuales del servicio"
                :icon="CreditCard"
            >
                <template #actions>
                    <Button
                        variant="outline"
                        class="rounded-xl border-primary/30 bg-card/80"
                        @click="router.post('/pagos/generar-mes')"
                    >
                        <CalendarPlus class="mr-2 size-4" />
                        Generar mes
                    </Button>
                    <Link href="/pagos/crear">
                        <Button class="rounded-xl shadow-lg shadow-primary/25">
                            <Plus class="mr-2 size-4" />
                            Registrar pago
                        </Button>
                    </Link>
                </template>
            </PageHeader>

            <TarifaBanner />

            <div class="isinuta-filter-bar">
                <SearchInput
                    v-model="q"
                    placeholder="Buscar afiliado o recibo..."
                    class="min-w-[220px] flex-1"
                />
                <FilterSelect v-model="estado" label="Estado">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="pagado">Pagado</option>
                </FilterSelect>
            </div>

            <DataCard title="Listado de pagos" :description="`${pagos.data.length} registros`" compact>
                <div v-if="pagos.data.length" class="isinuta-table-wrap isinuta-table-wrap-flush">
                    <table class="isinuta-table">
                        <thead>
                            <tr>
                                <th>Período</th>
                                <th>Afiliado</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th class="text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in pagos.data" :key="p.id">
                                <td class="font-medium capitalize">
                                    {{ mesNombre(p.mes) }} {{ p.anio }}
                                </td>
                                <td>
                                    {{ p.afiliado?.nombres }} {{ p.afiliado?.apellidos }}
                                </td>
                                <td class="font-bold text-primary">Bs {{ p.total }}</td>
                                <td><StatusBadge :status="p.estado" /></td>
                                <td class="text-right">
                                    <Link
                                        v-if="p.estado === 'pagado'"
                                        :href="`/pagos/${p.id}`"
                                        class="isinuta-table-link bg-primary/10 text-primary transition hover:bg-primary/20"
                                    >
                                        <Receipt class="size-3.5" />
                                        Ver recibo
                                    </Link>
                                    <Link
                                        v-else
                                        :href="`/pagos/crear?afiliado_id=${p.afiliado_id}`"
                                        class="isinuta-table-link bg-gradient-to-r from-cyan-500 to-teal-600 text-white shadow-md shadow-cyan-500/25"
                                    >
                                        Cobrar
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="isinuta-empty-state">
                    <CreditCard class="mb-3 size-10 text-muted-foreground/40" />
                    <p class="text-sm text-muted-foreground">No hay pagos con los filtros actuales.</p>
                </div>
            </DataCard>
        </div>
    </AppLayout>
</template>
