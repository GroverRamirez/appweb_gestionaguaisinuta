<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import FlashBanner from '@/components/isinuta/FlashBanner.vue';
import PageHeader from '@/components/isinuta/PageHeader.vue';
import SearchInput from '@/components/isinuta/SearchInput.vue';
import StatusBadge from '@/components/isinuta/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { CalendarPlus, Plus } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    pagos: { data: any[]; links: any[] };
    filtros: { q?: string; estado?: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Pagos', href: '/pagos' }];
const q = ref(props.filtros.q || '');
const estado = ref(props.filtros.estado || '');

watch([q, estado], () => {
    router.get('/pagos', { q: q.value, estado: estado.value }, { preserveState: true, replace: true });
});

const mesNombre = (mes: number) =>
    new Date(2000, mes - 1, 1).toLocaleString('es-BO', { month: 'long' });
</script>

<template>
    <Head title="Pagos" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6">
            <FlashBanner />
            <PageHeader title="Control de pagos" description="Cuota mensual: agua Bs 8 + alcantarillado Bs 15">
                <template #actions>
                    <Button variant="outline" class="rounded-xl" @click="router.post('/pagos/generar-mes')">
                        <CalendarPlus class="mr-2 size-4" />
                        Generar mes
                    </Button>
                    <Link href="/pagos/crear">
                        <Button class="rounded-xl shadow-md shadow-primary/20">
                            <Plus class="mr-2 size-4" />
                            Registrar pago
                        </Button>
                    </Link>
                </template>
            </PageHeader>
            <div class="flex flex-wrap gap-3">
                <SearchInput v-model="q" placeholder="Buscar afiliado o recibo..." />
                <select
                    v-model="estado"
                    class="h-11 rounded-xl border border-input bg-card/80 px-3 text-sm shadow-sm"
                >
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="pagado">Pagado</option>
                </select>
            </div>
            <DataCard>
                <div class="overflow-x-auto -mx-5 px-5">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                                <th class="pb-3 pr-4">Período</th>
                                <th class="pb-3 pr-4">Afiliado</th>
                                <th class="pb-3 pr-4">Total</th>
                                <th class="pb-3 pr-4">Estado</th>
                                <th class="pb-3 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <tr v-for="p in pagos.data" :key="p.id" class="hover:bg-muted/40">
                                <td class="py-3 capitalize">{{ mesNombre(p.mes) }} {{ p.anio }}</td>
                                <td class="py-3">{{ p.afiliado?.nombres }} {{ p.afiliado?.apellidos }}</td>
                                <td class="py-3 font-semibold">Bs {{ p.total }}</td>
                                <td class="py-3"><StatusBadge :status="p.estado" /></td>
                                <td class="py-3 text-right">
                                    <Link
                                        v-if="p.estado === 'pagado'"
                                        :href="`/pagos/${p.id}`"
                                        class="font-medium text-primary"
                                    >Ver recibo</Link>
                                    <Link
                                        v-else
                                        :href="`/pagos/crear?afiliado_id=${p.afiliado_id}`"
                                        class="font-medium text-primary"
                                    >Cobrar</Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </DataCard>
        </div>
    </AppLayout>
</template>
