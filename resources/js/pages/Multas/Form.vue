<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    afiliado: { id: number; ci: string; nombres: string; apellidos: string } | null;
    sugerencia: { tipo: string; monto: number; meses_mora: number } | null;
    deuda: { total: number } | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Multas', href: '/multas' },
    { title: 'Nueva', href: '/multas/crear' },
];

const busqueda = ref('');
const resultados = ref<Array<{ id: number; ci: string; nombres: string; apellidos: string }>>([]);

const form = useForm({
    afiliado_id: props.afiliado?.id ?? null,
    tipo: props.sugerencia?.tipo ?? 'trimestral',
    monto: props.sugerencia?.monto ?? 50,
    meses_mora: props.sugerencia?.meses_mora ?? 3,
    fecha_aplicacion: new Date().toISOString().slice(0, 10),
    observaciones: '',
});

const buscar = async () => {
    const res = await fetch(`/pagos/buscar-afiliado?q=${encodeURIComponent(busqueda.value)}`);
    resultados.value = await res.json();
};

const submit = () => form.post('/multas');
</script>

<template>
    <Head title="Nueva multa" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-xl flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold">Registrar multa</h1>
            <p v-if="deuda" class="rounded border p-3 text-sm">
                Deuda del afiliado: Bs {{ deuda.total }}
            </p>
            <div v-if="!afiliado" class="space-y-2">
                <Label>Buscar afiliado</Label>
                <Input v-model="busqueda" @keyup.enter="buscar" />
                <Button type="button" variant="outline" @click="buscar">Buscar</Button>
                <ul class="mt-2 space-y-1">
                    <li v-for="a in resultados" :key="a.id">
                        <Link :href="`/multas/crear?afiliado_id=${a.id}`" class="text-primary">
                            {{ a.ci }} — {{ a.nombres }} {{ a.apellidos }}
                        </Link>
                    </li>
                </ul>
            </div>
            <form v-else class="space-y-4 rounded-md border p-4" @submit.prevent="submit">
                <p class="font-medium">{{ afiliado.nombres }} {{ afiliado.apellidos }}</p>
                <div>
                    <Label>Tipo de multa</Label>
                    <select v-model="form.tipo" class="mt-1 h-10 w-full rounded-md border px-2 text-sm">
                        <option value="trimestral">Trimestral — Bs 50 (3+ meses)</option>
                        <option value="anual">Anual — Bs 150 (12+ meses)</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div>
                    <Label>Monto (Bs)</Label>
                    <Input v-model="form.monto" type="number" step="0.01" required />
                </div>
                <div>
                    <Label>Fecha de aplicación</Label>
                    <Input v-model="form.fecha_aplicacion" type="date" required />
                </div>
                <Button type="submit" :disabled="form.processing">Guardar multa</Button>
            </form>
        </div>
    </AppLayout>
</template>
