<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AfiliadoForm from '@/components/isinuta/AfiliadoForm.vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import FlashBanner from '@/components/isinuta/FlashBanner.vue';
import PageHeader from '@/components/isinuta/PageHeader.vue';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    afiliado: {
        id: number;
        ci: string;
        nombres: string;
        apellidos: string;
        telefono: string | null;
        direccion: string | null;
        fecha_afiliacion: string | null;
        estado: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Afiliados', href: '/afiliados' },
    { title: 'Editar afiliado', href: `/afiliados/${props.afiliado.id}/editar` },
];

const form = useForm({
    ci: props.afiliado.ci,
    nombres: props.afiliado.nombres,
    apellidos: props.afiliado.apellidos,
    telefono: props.afiliado.telefono ?? '',
    direccion: props.afiliado.direccion ?? '',
    fecha_afiliacion: props.afiliado.fecha_afiliacion ?? '',
    estado: props.afiliado.estado,
});

const submit = () => {
    form.put(`/afiliados/${props.afiliado.id}`);
};

const deleteAfiliado = () => {
    if (
        confirm(
            '¿Está seguro de que desea eliminar este afiliado? Esta acción no se puede deshacer.',
        )
    ) {
        form.delete(`/afiliados/${props.afiliado.id}`);
    }
};
</script>

<template>
    <Head title="Editar afiliado" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-3xl flex-1 flex-col gap-6">
            <FlashBanner />

            <PageHeader
                :title="`${afiliado.nombres} ${afiliado.apellidos}`"
                :description="`CI ${afiliado.ci} · actualizar información del afiliado`"
            >
                <template #actions>
                    <Link href="/afiliados">
                        <Button variant="outline" class="rounded-xl">Volver al listado</Button>
                    </Link>
                </template>
            </PageHeader>

            <DataCard title="Datos del afiliado" description="Modifique los campos necesarios">
                <AfiliadoForm
                    :form="form"
                    submit-label="Actualizar afiliado"
                    show-delete
                    @submit="submit"
                    @delete="deleteAfiliado"
                />
            </DataCard>
        </div>
    </AppLayout>
</template>
