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
    pendientes: Array<{ id: number; mes: number; anio: number; total: string }>;
    numero_sugerido: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Pagos', href: '/pagos' },
    { title: 'Registrar pago', href: '/pagos/crear' },
];

const busqueda = ref('');
const resultados = ref<Array<{ id: number; ci: string; nombres: string; apellidos: string }>>([]);
const afiliadoSel = ref(props.afiliado);
const pendientes = ref(props.pendientes);

const form = useForm({
    pago_id: pendientes.value[0]?.id ?? null,
    fecha_pago: new Date().toISOString().slice(0, 10),
    metodo: 'efectivo',
    observaciones: '',
});

const buscar = async () => {
    if (busqueda.value.length < 2) return;
    const res = await fetch(`/pagos/buscar-afiliado?q=${encodeURIComponent(busqueda.value)}`);
    resultados.value = await res.json();
};

const seleccionar = (a: { id: number; ci: string; nombres: string; apellidos: string }) => {
    window.location.href = `/pagos/crear?afiliado_id=${a.id}`;
};

const mesNombre = (mes: number) =>
    new Date(2000, mes - 1, 1).toLocaleString('es-BO', { month: 'long' });

const submit = () => form.post('/pagos');
</script>

<template>
    <Head title="Registrar pago" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex max-w-2xl flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold">Registrar pago mensual</h1>
            <p class="text-sm text-muted-foreground">
                Agua Bs 8 + Alcantarillado Bs 15 = Bs 23 por período
            </p>

            <div v-if="!afiliadoSel" class="rounded-md border p-4">
                <Label>Buscar afiliado</Label>
                <div class="mt-2 flex gap-2">
                    <Input v-model="busqueda" placeholder="CI o nombre..." @keyup.enter="buscar" />
                    <Button type="button" variant="outline" @click="buscar">Buscar</Button>
                </div>
                <ul v-if="resultados.length" class="mt-3 space-y-1">
                    <li
                        v-for="a in resultados"
                        :key="a.id"
                        class="cursor-pointer rounded px-2 py-1 hover:bg-muted"
                        @click="seleccionar(a)"
                    >
                        {{ a.ci }} — {{ a.nombres }} {{ a.apellidos }}
                    </li>
                </ul>
            </div>

            <form v-else class="space-y-4 rounded-md border p-6" @submit.prevent="submit">
                <p class="font-medium">
                    {{ afiliadoSel.nombres }} {{ afiliadoSel.apellidos }} ({{ afiliadoSel.ci }})
                </p>
                <div class="space-y-2">
                    <Label>Período a pagar</Label>
                    <select
                        v-model="form.pago_id"
                        class="h-10 w-full rounded-md border px-3 text-sm"
                        required
                    >
                        <option v-for="p in pendientes" :key="p.id" :value="p.id">
                            {{ mesNombre(p.mes) }} {{ p.anio }} — Bs {{ p.total }}
                        </option>
                    </select>
                </div>
                <div class="space-y-2">
                    <Label>Fecha de pago</Label>
                    <Input v-model="form.fecha_pago" type="date" required />
                </div>
                <div class="space-y-2">
                    <Label>Método</Label>
                    <select v-model="form.metodo" class="h-10 w-full rounded-md border px-3 text-sm">
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="qr">QR</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <Label>Observaciones</Label>
                    <Input v-model="form.observaciones" />
                </div>
                <p class="text-sm text-muted-foreground">Recibo sugerido: {{ numero_sugerido }}</p>
                <div class="flex justify-end gap-2">
                    <Link href="/pagos"><Button variant="outline" type="button">Cancelar</Button></Link>
                    <Button type="submit" :disabled="form.processing || !pendientes.length">
                        Confirmar pago
                    </Button>
                </div>
                <p v-if="!pendientes.length" class="text-sm text-amber-600">
                    No hay períodos pendientes para este afiliado.
                </p>
            </form>
        </div>
    </AppLayout>
</template>
