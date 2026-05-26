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
import { etiquetaDe, etiquetaTipoMulta } from '@/lib/etiquetas';
import { Plus } from 'lucide-vue-next';
import { ref, watch } from 'vue';

defineProps<{
    multas: { data: any[]; links: any[] };
    filtros: { q?: string; estado?: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Multas', href: '/multas' }];
const q = ref('');
const estado = ref('');

watch([q, estado], () => {
    router.get('/multas', { q: q.value, estado: estado.value }, { preserveState: true, replace: true });
});

const marcarPagada = (id: number) => router.post(`/multas/${id}/pagar`);
</script>

<template>
    <Head title="Multas" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6">
            <FlashBanner />
            <PageHeader title="Multas por mora" description="Bs 50 trimestral · Bs 150 anual">
                <template #actions>
                    <Link href="/multas/crear">
                        <Button class="rounded-xl"><Plus class="mr-2 size-4" />Nueva multa</Button>
                    </Link>
                </template>
            </PageHeader>
            <div class="flex flex-wrap gap-3">
                <SearchInput v-model="q" placeholder="Buscar afiliado..." />
                <select v-model="estado" class="h-11 rounded-xl border px-3 text-sm">
                    <option value="">Todas</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="pagada">Pagada</option>
                </select>
            </div>
            <DataCard>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-xs uppercase text-muted-foreground">
                            <th class="pb-3">Afiliado</th>
                            <th class="pb-3">Tipo</th>
                            <th class="pb-3">Monto</th>
                            <th class="pb-3">Estado</th>
                            <th class="pb-3 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="m in multas.data" :key="m.id" class="hover:bg-muted/40">
                            <td class="py-3">{{ m.afiliado?.nombres }} {{ m.afiliado?.apellidos }}</td>
                            <td class="py-3">{{ etiquetaDe(m.tipo, etiquetaTipoMulta) }} ({{ m.meses_mora }} m.)</td>
                            <td class="py-3 font-semibold">Bs {{ m.monto }}</td>
                            <td class="py-3"><StatusBadge :status="m.estado" /></td>
                            <td class="py-3 text-right">
                                <Button
                                    v-if="m.estado === 'pendiente'"
                                    size="sm"
                                    variant="outline"
                                    class="rounded-lg"
                                    @click="marcarPagada(m.id)"
                                >Marcar pagada</Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </DataCard>
        </div>
    </AppLayout>
</template>
