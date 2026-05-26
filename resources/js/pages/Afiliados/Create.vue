<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AfiliadoForm from '@/components/isinuta/AfiliadoForm.vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import FlashBanner from '@/components/isinuta/FlashBanner.vue';
import PageHeader from '@/components/isinuta/PageHeader.vue';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Afiliados', href: '/afiliados' },
    { title: 'Nuevo afiliado', href: '/afiliados/crear' },
];

const form = useForm({
    ci: '',
    nombres: '',
    apellidos: '',
    telefono: '',
    direccion: '',
    fecha_afiliacion: new Date().toISOString().slice(0, 10),
    estado: 'activo',
});

const submit = () => {
    form.post('/afiliados');
};
</script>

<template>
    <Head title="Nuevo afiliado" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-3xl flex-1 flex-col gap-6">
            <FlashBanner />

            <PageHeader
                title="Registrar afiliado"
                description="Complete los datos del nuevo socio de la asociación"
            >
                <template #actions>
                    <Link href="/afiliados">
                        <Button variant="outline" class="rounded-xl">Cancelar</Button>
                    </Link>
                </template>
            </PageHeader>

            <DataCard title="Datos del afiliado" description="Campos obligatorios marcados en el formulario">
                <AfiliadoForm :form="form" submit-label="Guardar afiliado" @submit="submit" />
            </DataCard>
        </div>
    </AppLayout>
</template>
