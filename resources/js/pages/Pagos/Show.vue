<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { etiquetaDe, etiquetaMetodoPago } from '@/lib/etiquetas';
import { Droplets, Printer } from 'lucide-vue-next';

defineProps<{
    pago: {
        id: number;
        numero_recibo: string;
        mes: number;
        anio: number;
        monto_agua: string;
        monto_alcantarillado: string;
        total: string;
        fecha_pago: string;
        metodo: string;
        afiliado: { ci: string; nombres: string; apellidos: string; direccion?: string };
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Pagos', href: '/pagos' },
    { title: 'Comprobante', href: '#' },
];

const mesNombre = (mes: number) =>
    new Date(2000, mes - 1, 1).toLocaleString('es-BO', { month: 'long' });

const imprimir = () => window.print();
</script>

<template>
    <Head title="Comprobante de pago" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex max-w-lg flex-1 justify-center py-4">
            <div
                class="isinuta-card w-full overflow-hidden border-2 border-primary/20 p-0 shadow-xl shadow-primary/10 print:border print:shadow-none"
            >
                <div
                    class="relative overflow-hidden bg-gradient-to-br from-cyan-600 via-teal-600 to-emerald-600 px-8 py-8 text-center text-white"
                >
                    <div class="isinuta-water-waves opacity-60" aria-hidden="true" />
                    <Droplets class="relative mx-auto mb-2 size-12 opacity-95" />
                    <h1 class="text-sm font-bold tracking-wide uppercase">
                        Asociación ISINUTA
                    </h1>
                    <p class="text-xs text-white/80">Comprobante de pago</p>
                </div>
                <div class="space-y-4 p-8">
                    <p class="text-center font-mono text-2xl font-bold tracking-wider text-primary">
                        {{ pago.numero_recibo }}
                    </p>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between border-b border-dashed pb-2">
                            <dt class="text-muted-foreground">Afiliado</dt>
                            <dd class="text-right font-medium">
                                {{ pago.afiliado.nombres }} {{ pago.afiliado.apellidos }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted-foreground">CI</dt>
                            <dd>{{ pago.afiliado.ci }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted-foreground">Período</dt>
                            <dd class="capitalize">{{ mesNombre(pago.mes) }} {{ pago.anio }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted-foreground">Agua potable</dt>
                            <dd>Bs {{ pago.monto_agua }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted-foreground">Alcantarillado</dt>
                            <dd>Bs {{ pago.monto_alcantarillado }}</dd>
                        </div>
                        <div
                            class="flex justify-between rounded-xl bg-primary/5 px-4 py-3 text-lg font-bold"
                        >
                            <dt>Total</dt>
                            <dd class="text-primary">Bs {{ pago.total }}</dd>
                        </div>
                        <div class="flex justify-between text-xs">
                            <dt class="text-muted-foreground">Fecha · Método</dt>
                            <dd>{{ pago.fecha_pago }} · {{ etiquetaDe(pago.metodo, etiquetaMetodoPago) }}</dd>
                        </div>
                    </dl>
                </div>
                <div class="flex justify-center gap-3 border-t bg-muted/30 p-4 print:hidden">
                    <Button variant="outline" class="rounded-xl" @click="imprimir">
                        <Printer class="mr-2 size-4" />
                        Imprimir
                    </Button>
                    <Link href="/pagos">
                        <Button class="rounded-xl">Volver</Button>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
