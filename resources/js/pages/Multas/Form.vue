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
import { AlertTriangle, User } from 'lucide-vue-next';

const props = defineProps<{
    afiliado: { id: number; ci: string; nombres: string; apellidos: string } | null;
    sugerencia: { tipo: string; monto: number; meses_mora: number } | null;
    deuda: { total: number } | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Multas', href: '/multas' },
    { title: 'Nueva multa', href: '/multas/crear' },
];

const form = useForm({
    afiliado_id: props.afiliado?.id ?? null,
    tipo: props.sugerencia?.tipo ?? 'trimestral',
    monto: props.sugerencia?.monto ?? 50,
    meses_mora: props.sugerencia?.meses_mora ?? 3,
    fecha_aplicacion: new Date().toISOString().slice(0, 10),
    observaciones: '',
});

const submit = () => form.post('/multas');
</script>

<template>
    <Head title="Nueva multa" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-2xl flex-1 flex-col gap-6">
            <PageHeader
                title="Registrar multa"
                description="Bs 50 trimestral (3+ meses) · Bs 150 anual (12+ meses)"
                :icon="AlertTriangle"
            />

            <AfiliadoSearch v-if="!afiliado" redirect-path="/multas/crear" />

            <template v-else>
                <div class="isinuta-afiliado-chip">
                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-lg"
                    >
                        <User class="size-5" />
                    </div>
                    <div>
                        <p class="font-bold">{{ afiliado.nombres }} {{ afiliado.apellidos }}</p>
                        <p class="text-sm text-muted-foreground">CI {{ afiliado.ci }}</p>
                    </div>
                    <Link href="/multas/crear" class="ml-auto text-xs font-semibold text-primary">
                        Cambiar
                    </Link>
                </div>

                <p v-if="deuda" class="isinuta-alert-danger">
                    Deuda actual del afiliado: <strong>Bs {{ deuda.total }}</strong>
                </p>

                <DataCard title="Datos de la multa">
                    <form class="space-y-5" @submit.prevent="submit">
                        <div class="grid gap-2">
                            <Label>Tipo de multa</Label>
                            <select v-model="form.tipo" class="isinuta-input">
                                <option value="trimestral">Trimestral — Bs 50 (3+ meses)</option>
                                <option value="anual">Anual — Bs 150 (12+ meses)</option>
                                <option value="otro">Otro monto</option>
                            </select>
                            <InputError :message="form.errors.tipo" />
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label>Monto (Bs)</Label>
                                <Input
                                    v-model="form.monto"
                                    type="number"
                                    step="0.01"
                                    class="h-11 rounded-xl"
                                    required
                                />
                                <InputError :message="form.errors.monto" />
                            </div>
                            <div class="grid gap-2">
                                <Label>Fecha de aplicación</Label>
                                <Input
                                    v-model="form.fecha_aplicacion"
                                    type="date"
                                    class="h-11 rounded-xl"
                                    required
                                />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label>Observaciones</Label>
                            <Input v-model="form.observaciones" class="h-11 rounded-xl" />
                        </div>

                        <div class="isinuta-form-footer">
                            <Link href="/multas">
                                <Button variant="outline" type="button" class="rounded-xl">
                                    Cancelar
                                </Button>
                            </Link>
                            <Button
                                type="submit"
                                class="rounded-xl shadow-lg shadow-primary/25"
                                :disabled="form.processing"
                            >
                                Guardar multa
                            </Button>
                        </div>
                    </form>
                </DataCard>
            </template>
        </div>
    </AppLayout>
</template>
