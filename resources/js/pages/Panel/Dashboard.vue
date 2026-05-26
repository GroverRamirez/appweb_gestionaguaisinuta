<script setup lang="ts">

import AppLayout from '@/layouts/AppLayout.vue';

import DataCard from '@/components/isinuta/DataCard.vue';

import FlashBanner from '@/components/isinuta/FlashBanner.vue';

import StatCard from '@/components/isinuta/StatCard.vue';

import { Button } from '@/components/ui/button';

import type { BreadcrumbItem } from '@/types';

import { Head, Link } from '@inertiajs/vue3';

import {

    AlertTriangle,

    ClipboardList,

    CreditCard,

    Droplets,

    TrendingUp,

    Users,

} from 'lucide-vue-next';

import { computed, withDefaults } from 'vue';



const props = withDefaults(
    defineProps<{
    totales: {

        afiliados_activos: number;

        pagos_pendientes: number;

        monto_pendiente: number;

        multas_pendientes: number;

        tramites_pendientes: number;

        recaudacion_mes: number;

    };

    ultimos_pagos: Array<{

        id: number;

        numero_recibo: string;

        total: string;

        fecha_pago: string;

        afiliado: { ci: string; nombres: string; apellidos: string };

    }>;

    serie_recaudacion: Array<{ periodo: string; monto: number }>;
    }>(),
    {
        ultimos_pagos: () => [],
        serie_recaudacion: () => [],
    },
);



const breadcrumbs: BreadcrumbItem[] = [{ title: 'Panel', href: '/panel' }];



const formatBs = (monto: number) =>

    new Intl.NumberFormat('es-BO', { style: 'currency', currency: 'BOB' }).format(monto);



const maxRecaudacion = computed(() =>

    Math.max(...props.serie_recaudacion.map((s) => s.monto), 1),

);

</script>



<template>

    <Head title="Panel ISINUTA" />



    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="flex flex-1 flex-col gap-6">

            <FlashBanner />



            <section

                class="isinuta-hero relative overflow-hidden rounded-3xl px-6 py-8 text-white md:px-8 md:py-10"

            >

                <div

                    aria-hidden="true"

                    class="pointer-events-none absolute -right-16 -top-16 size-56 rounded-full bg-white/10 blur-2xl"

                />

                <div

                    aria-hidden="true"

                    class="pointer-events-none absolute -bottom-20 -left-10 size-48 rounded-full bg-cyan-300/20 blur-3xl"

                />

                <div class="relative flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

                    <div class="max-w-xl space-y-3">

                        <div

                            class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold backdrop-blur-sm"

                        >

                            <Droplets class="size-3.5" />

                            ISINUTA · Villa Tunari

                        </div>

                        <h2 class="text-2xl font-extrabold tracking-tight md:text-3xl">

                            Bienvenido al panel administrativo

                        </h2>

                        <p class="text-sm leading-relaxed text-white/85">

                            Gestione afiliados, cobros, multas y trámites con una vista clara del

                            estado del servicio de agua potable.

                        </p>

                    </div>

                    <Link href="/pagos/crear" class="relative shrink-0">

                        <Button

                            size="lg"

                            class="h-12 rounded-xl border-0 bg-white font-semibold text-cyan-700 shadow-lg shadow-black/10 hover:bg-white/95"

                        >

                            <CreditCard class="mr-2 size-5" />

                            Registrar pago

                        </Button>

                    </Link>

                </div>

            </section>



            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

                <StatCard

                    title="Afiliados activos"

                    :value="totales.afiliados_activos"

                    :icon="Users"

                    variant="info"

                />

                <StatCard

                    title="Recaudación del mes"

                    :value="formatBs(totales.recaudacion_mes)"

                    :icon="TrendingUp"

                    variant="success"

                />

                <StatCard

                    title="Deuda pendiente"

                    :value="formatBs(totales.monto_pendiente)"

                    :subtitle="`${totales.pagos_pendientes} períodos sin pagar`"

                    :icon="CreditCard"

                    variant="warning"

                />

                <StatCard

                    title="Multas pendientes"

                    :value="totales.multas_pendientes"

                    :icon="AlertTriangle"

                    variant="danger"

                />

                <StatCard

                    title="Trámites pendientes"

                    :value="totales.tramites_pendientes"

                    :icon="ClipboardList"

                    variant="default"

                />

            </div>



            <div class="grid gap-6 lg:grid-cols-2">

                <DataCard title="Recaudación últimos 6 meses" description="Evolución mensual">

                    <ul class="space-y-4">

                        <li v-for="item in serie_recaudacion" :key="item.periodo" class="space-y-1.5">

                            <div class="flex justify-between text-sm">

                                <span class="font-semibold">{{ item.periodo }}</span>

                                <span class="font-medium text-primary">{{

                                    formatBs(item.monto)

                                }}</span>

                            </div>

                            <div class="h-2.5 overflow-hidden rounded-full bg-muted/80">

                                <div

                                    class="h-full rounded-full bg-gradient-to-r from-cyan-400 via-teal-500 to-emerald-500 transition-all duration-700 ease-out"

                                    :style="{

                                        width: `${Math.max(4, (item.monto / maxRecaudacion) * 100)}%`,

                                    }"

                                />

                            </div>

                        </li>

                    </ul>

                </DataCard>



                <DataCard title="Últimos pagos" description="Movimientos recientes">

                    <ul v-if="ultimos_pagos.length" class="divide-y divide-border/40">

                        <li

                            v-for="pago in ultimos_pagos"

                            :key="pago.id"

                            class="flex items-center justify-between gap-4 py-3.5 first:pt-0"

                        >

                            <div class="min-w-0">

                                <p class="truncate font-semibold">

                                    {{ pago.afiliado.nombres }} {{ pago.afiliado.apellidos }}

                                </p>

                                <p class="text-xs text-muted-foreground">

                                    {{ pago.numero_recibo }}

                                </p>

                            </div>

                            <span

                                class="shrink-0 rounded-lg bg-primary/10 px-2.5 py-1 text-sm font-bold text-primary"

                            >

                                {{ formatBs(Number(pago.total)) }}

                            </span>

                        </li>

                    </ul>

                    <p v-else class="py-10 text-center text-sm text-muted-foreground">

                        Aún no hay pagos registrados este período.

                    </p>

                </DataCard>

            </div>

        </div>

    </AppLayout>

</template>

