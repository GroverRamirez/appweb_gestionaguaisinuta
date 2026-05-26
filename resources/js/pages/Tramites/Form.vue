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
    deuda: { total: number; pagos: number; multas: number } | null;
    sin_deudas: boolean;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Trámites', href: '/tramites' },
    { title: 'Nuevo', href: '/tramites/crear' },
];

const busqueda = ref('');
const resultados = ref<Array<{ id: number; ci: string; nombres: string; apellidos: string }>>([]);
const form = useForm({
    afiliado_id: props.afiliado?.id ?? null,
    ci_nuevo: '',
    nombres_nuevo: '',
    apellidos_nuevo: '',
    observaciones: '',
});

const buscar = async () => {
    const res = await fetch(`/pagos/buscar-afiliado?q=${encodeURIComponent(busqueda.value)}`);
    resultados.value = await res.json();
};

const submit = () => {
    if (!props.sin_deudas) {
        alert('El afiliado tiene deudas pendientes. Debe regularizar antes del cambio de nombre.');
        return;
    }
    form.post('/tramites');
};
</script>

<template>
    <Head title="Trámite cambio de nombre" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-xl flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold">Trámite de cambio de nombre</h1>
            <p class="text-sm text-muted-foreground">
                Verifique que no existan deudas antes de registrar la solicitud.
            </p>

            <div v-if="!afiliado" class="space-y-2 rounded-md border p-4">
                <Label>Buscar afiliado actual</Label>
                <Input v-model="busqueda" @keyup.enter="buscar" />
                <Button type="button" variant="outline" @click="buscar">Buscar</Button>
                <ul class="mt-2 space-y-1">
                    <li v-for="a in resultados" :key="a.id">
                        <Link
                            :href="`/tramites/crear?afiliado_id=${a.id}`"
                            class="text-primary hover:underline"
                        >
                            {{ a.ci }} — {{ a.nombres }} {{ a.apellidos }}
                        </Link>
                    </li>
                </ul>
            </div>

            <form v-else class="space-y-4 rounded-md border p-4" @submit.prevent="submit">
                <p class="font-medium">Titular actual: {{ afiliado.nombres }} {{ afiliado.apellidos }}</p>
                <p
                    class="rounded p-2 text-sm"
                    :class="sin_deudas ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'"
                >
                    <template v-if="deuda">
                        Deuda verificada: Bs {{ deuda.total }}
                        (pagos: {{ deuda.pagos }}, multas: {{ deuda.multas }})
                    </template>
                    {{ sin_deudas ? '✓ Sin deudas — puede continuar' : '✗ Tiene deudas pendientes' }}
                </p>
                <div>
                    <Label>CI nuevo titular</Label>
                    <Input v-model="form.ci_nuevo" required />
                </div>
                <div>
                    <Label>Nombres</Label>
                    <Input v-model="form.nombres_nuevo" required />
                </div>
                <div>
                    <Label>Apellidos</Label>
                    <Input v-model="form.apellidos_nuevo" required />
                </div>
                <div>
                    <Label>Observaciones</Label>
                    <Input v-model="form.observaciones" />
                </div>
                <div class="flex gap-2">
                    <Link href="/tramites"><Button variant="outline" type="button">Cancelar</Button></Link>
                    <Button type="submit" :disabled="form.processing || !sin_deudas">Registrar trámite</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
