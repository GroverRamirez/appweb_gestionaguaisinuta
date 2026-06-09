<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Shield } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import FlashBanner from '@/components/isinuta/FlashBanner.vue';
import PageHeader from '@/components/isinuta/PageHeader.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    roles_disponibles: Array<{ value: string; label: string }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Gestión de usuarios', href: '/usuarios' },
    { title: 'Nuevo usuario', href: '/usuarios/crear' },
];

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'cajera',
});

const submit = () => {
    form.post('/usuarios');
};
</script>

<template>
    <Head title="Nuevo usuario" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-3xl flex-1 flex-col gap-6">
            <FlashBanner />

            <PageHeader
                title="Nuevo usuario"
                description="Registre una cuenta con acceso al sistema ISINUTA"
                :icon="Shield"
            >
                <template #actions>
                    <Link href="/usuarios">
                        <Button variant="outline" class="rounded-xl">Cancelar</Button>
                    </Link>
                </template>
            </PageHeader>

            <DataCard
                title="Datos del usuario"
                description="El correo quedará verificado automáticamente al ser creado por un administrador"
            >
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="name">Nombre completo</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                class="h-11 rounded-xl"
                                required
                                autocomplete="name"
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
                                autocomplete="email"
                            />
                            <InputError :message="form.errors.email" />
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="password">Contraseña</Label>
                            <PasswordInput
                                id="password"
                                v-model="form.password"
                                class="h-11 rounded-xl"
                                required
                                autocomplete="new-password"
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password_confirmation">Confirmar contraseña</Label>
                            <PasswordInput
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                class="h-11 rounded-xl"
                                required
                                autocomplete="new-password"
                            />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="role">Rol del sistema</Label>
                        <select
                            id="role"
                            v-model="form.role"
                            class="isinuta-input"
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
                        <p class="text-xs text-muted-foreground">
                            <strong>Administrador:</strong> acceso total, incluida esta sección.
                            <strong class="ml-1">Cajera:</strong> operación diaria (pagos, afiliados, reportes).
                            <strong class="ml-1">Operador:</strong> mismo acceso que cajera (rol legado).
                        </p>
                    </div>

                    <div class="flex justify-end border-t border-border/60 pt-6">
                        <Button
                            type="submit"
                            class="rounded-xl shadow-md shadow-primary/20"
                            :disabled="form.processing"
                        >
                            Crear usuario
                        </Button>
                    </div>
                </form>
            </DataCard>
        </div>
    </AppLayout>
</template>
