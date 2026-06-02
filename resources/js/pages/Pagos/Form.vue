<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AfiliadoSearch from '@/components/isinuta/AfiliadoSearch.vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/isinuta/PageHeader.vue';
import TarifaBanner from '@/components/isinuta/TarifaBanner.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { CreditCard, User } from 'lucide-vue-next';

const props = defineProps<{
    afiliado: { id: number; ci: string; nombres: string; apellidos: string } | null;
    pendientes: Array<{ id: number; mes: number; anio: number; total: string }>;
    numero_sugerido: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Pagos', href: '/pagos' },
    { title: 'Registrar pago', href: '/pagos/crear' },
];

const form = useForm({
    pago_id: props.pendientes[0]?.id ?? null,
    fecha_pago: new Date().toISOString().slice(0, 10),
    metodo: 'efectivo',
    observaciones: '',
});

const mesNombre = (mes: number) =>
    new Date(2000, mes - 1, 1).toLocaleString('es-BO', { month: 'long' });

const submit = () => form.post('/pagos');
</script>

<template>
    <Head title="Registrar pago" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex w-full max-w-2xl flex-1 flex-col gap-6">
            <PageHeader
                title="Registrar pago mensual"
                description="Seleccione el afiliado y confirme el cobro"
                :icon="CreditCard"
            />

            <TarifaBanner />

            <AfiliadoSearch
                v-if="!afiliado"
                redirect-path="/pagos/crear"
            />

            <template v-else>
                <div class="isinuta-afiliado-chip">
                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500 to-teal-600 text-white shadow-lg"
                    >
                        <User class="size-5" />
                    </div>
                    <div>
                        <p class="font-bold">{{ afiliado.nombres }} {{ afiliado.apellidos }}</p>
                        <p class="text-sm text-muted-foreground">CI {{ afiliado.ci }}</p>
                    </div>
                    <Link href="/pagos/crear" class="ml-auto text-xs font-semibold text-primary">
                        Cambiar
                    </Link>
                </div>

                <DataCard
                    title="Datos del cobro"
                    :description="`Recibo sugerido: ${numero_sugerido}`"
                >
                    <form class="space-y-5" @submit.prevent="submit">
                        <div class="grid gap-2">
                            <Label>Período a pagar</Label>
                            <select v-model="form.pago_id" class="isinuta-input" required>
                                <option v-for="p in pendientes" :key="p.id" :value="p.id">
                                    {{ mesNombre(p.mes) }} {{ p.anio }} — Bs {{ p.total }}
                                </option>
                            </select>
                            <InputError :message="form.errors.pago_id" />
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label>Fecha de pago</Label>
                                <Input
                                    v-model="form.fecha_pago"
                                    type="date"
                                    class="h-11 rounded-xl"
                                    required
                                />
                                <InputError :message="form.errors.fecha_pago" />
                            </div>
                            <div class="grid gap-2">
                                <Label>Método de pago</Label>
                                <select v-model="form.metodo" class="isinuta-input">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="qr">QR</option>
                                    <option value="otro">Otro</option>
                                </select>
                                <InputError :message="form.errors.metodo" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label>Observaciones (opcional)</Label>
                            <Input v-model="form.observaciones" class="h-11 rounded-xl" />
                        </div>

                        <p
                            v-if="!pendientes.length"
                            class="isinuta-alert-danger"
                        >
                            No hay períodos pendientes para este afiliado.
                        </p>

                        <div class="isinuta-form-footer">
                            <Link href="/pagos">
                                <Button variant="outline" type="button" class="rounded-xl">
                                    Cancelar
                                </Button>
                            </Link>
                            <Button
                                type="submit"
                                class="rounded-xl shadow-lg shadow-primary/25"
                                :disabled="form.processing || !pendientes.length"
                            >
                                Confirmar pago — Bs 23,00
                            </Button>
                        </div>
                    </form>
                </DataCard>
            </template>
        </div>
    </AppLayout>
</template>
