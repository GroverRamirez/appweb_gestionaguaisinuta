<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import FlashBanner from '@/components/isinuta/FlashBanner.vue';
import PageHeader from '@/components/isinuta/PageHeader.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    usuario: {
        id: number;
        name: string;
        email: string;
        role: string;
        estado: string;
    };
    roles_disponibles: Array<{ value: string; label: string }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Usuarios', href: '/usuarios' },
    { title: 'Editar usuario', href: `/usuarios/${props.usuario.id}/editar` },
];

const form = useForm({
    name: props.usuario.name,
    email: props.usuario.email,
    role: props.usuario.role,
    estado: props.usuario.estado,
});

const submit = () => {
    form.put(`/usuarios/${props.usuario.id}`);
};
</script>

<template>
    <Head title="Editar usuario" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-3xl flex-1 flex-col gap-6">
            <FlashBanner />

            <PageHeader
                :title="usuario.name"
                description="Actualice datos y rol de acceso al sistema"
            >
                <template #actions>
                    <Link href="/usuarios">
                        <Button variant="outline" class="rounded-xl">Volver</Button>
                    </Link>
                </template>
            </PageHeader>

            <DataCard title="Datos del usuario" description="Solo administradores pueden gestionar usuarios">
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="name">Nombre completo</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                class="h-11 rounded-xl"
                                required
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Correo electrónico</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="h-11 rounded-xl"
                                required
                            />
                            <InputError :message="form.errors.email" />
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="role">Rol del sistema</Label>
                            <select
                                id="role"
                                v-model="form.role"
                                class="flex h-11 w-full rounded-xl border border-input bg-background px-3 text-sm shadow-sm focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                            >
                                <option
                                    v-for="rol in roles_disponibles"
                                    :key="rol.value"
                                    :value="rol.value"
                                >
                                    {{ rol.label }}
                                </option>
                            </select>
                            <InputError :message="form.errors.role" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="estado">Estado de la cuenta</Label>
                            <select
                                id="estado"
                                v-model="form.estado"
                                class="flex h-11 w-full rounded-xl border border-input bg-background px-3 text-sm shadow-sm focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                            >
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                            <InputError :message="form.errors.estado" />
                        </div>
                    </div>

                    <p class="text-xs text-muted-foreground">
                            <strong>Administrador:</strong> acceso total, incluida esta sección.
                            <strong class="ml-1">Cajera:</strong> operación diaria (pagos, afiliados, reportes).
                            <strong class="ml-1">Operador:</strong> mismo acceso que cajera (rol legado).
                        Los usuarios inactivos no pueden iniciar sesión.
                    </p>

                    <div class="flex justify-end border-t border-border/60 pt-6">
                        <Button
                            type="submit"
                            class="rounded-xl shadow-md shadow-primary/20"
                            :disabled="form.processing"
                        >
                            Guardar cambios
                        </Button>
                    </div>
                </form>
            </DataCard>
        </div>
    </AppLayout>
</template>
