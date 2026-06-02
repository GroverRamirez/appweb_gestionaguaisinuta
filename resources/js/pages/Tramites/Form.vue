<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AfiliadoSearch from '@/components/isinuta/AfiliadoSearch.vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/isinuta/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { AlertTriangle, ClipboardList, User } from 'lucide-vue-next';

const props = defineProps<{
    afiliado: { id: number; ci: string; nombres: string; apellidos: string } | null;
    deuda: { total: number; pagos: number; multas: number } | null;
    sin_deudas: boolean;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Trámites', href: '/tramites' },
    { title: 'Nuevo trámite', href: '/tramites/crear' },
];

const form = useForm({
    afiliado_id: props.afiliado?.id ?? null,
    ci_nuevo: '',
    nombres_nuevo: '',
    apellidos_nuevo: '',
    observaciones: '',
});

const submit = () => {
    if (!props.sin_deudas) {
        return;
    }
    form.post('/tramites');
};
</script>

<template>
    <Head title="Trámite cambio de titular" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-2xl flex-1 flex-col gap-6">
            <PageHeader
                title="Cambio de titular"
                description="Verifique que no existan deudas antes de registrar la solicitud"
                :icon="ClipboardList"
            />

            <AfiliadoSearch
                v-if="!afiliado"
                redirect-path="/tramites/crear"
                label="Buscar titular actual"
            />

            <template v-else>
                <div class="isinuta-afiliado-chip">
                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 text-white shadow-lg"
                    >
                        <User class="size-5" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                            Titular actual
                        </p>
                        <p class="font-bold">{{ afiliado.nombres }} {{ afiliado.apellidos }}</p>
                    </div>
                    <Link href="/tramites/crear" class="ml-auto text-xs font-semibold text-primary">
                        Cambiar
                    </Link>
                </div>

                <div
                    v-if="deuda"
                    :class="sin_deudas ? 'isinuta-alert-success' : 'isinuta-alert-danger'"
                >
                    <p class="flex items-center gap-2 font-semibold">
                        <AlertTriangle v-if="!sin_deudas" class="size-4" />
                        Deuda verificada: Bs {{ deuda.total }}
                        <span class="font-normal text-muted-foreground">
                            (pagos: Bs {{ deuda.pagos }}, multas: Bs {{ deuda.multas }})
                        </span>
                    </p>
                    <p class="mt-1">
                        {{
                            sin_deudas
                                ? '✓ Sin deudas — puede continuar con el trámite'
                                : '✗ Regularice las deudas antes de continuar'
                        }}
                    </p>
                </div>

                <DataCard title="Datos del nuevo titular">
                    <form class="space-y-5" @submit.prevent="submit">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="grid gap-2 sm:col-span-2">
                                <Label>CI del nuevo titular</Label>
                                <Input
                                    v-model="form.ci_nuevo"
                                    class="h-11 rounded-xl"
                                    required
                                />
                                <InputError :message="form.errors.ci_nuevo" />
                            </div>
                            <div class="grid gap-2">
                                <Label>Nombres</Label>
                                <Input
                                    v-model="form.nombres_nuevo"
                                    class="h-11 rounded-xl"
                                    required
                                />
                                <InputError :message="form.errors.nombres_nuevo" />
                            </div>
                            <div class="grid gap-2">
                                <Label>Apellidos</Label>
                                <Input
                                    v-model="form.apellidos_nuevo"
                                    class="h-11 rounded-xl"
                                    required
                                />
                                <InputError :message="form.errors.apellidos_nuevo" />
                            </div>
                            <div class="grid gap-2 sm:col-span-2">
                                <Label>Observaciones</Label>
                                <Input v-model="form.observaciones" class="h-11 rounded-xl" />
                            </div>
                        </div>

                        <div class="isinuta-form-footer">
                            <Link href="/tramites">
                                <Button variant="outline" type="button" class="rounded-xl">
                                    Cancelar
                                </Button>
                            </Link>
                            <Button
                                type="submit"
                                class="rounded-xl shadow-lg shadow-primary/25"
                                :disabled="form.processing || !sin_deudas"
                            >
                                Registrar trámite
                            </Button>
                        </div>
                    </form>
                </DataCard>
            </template>
        </div>
    </AppLayout>
</template>
