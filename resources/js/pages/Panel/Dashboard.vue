<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import DataCard from '@/components/isinuta/DataCard.vue';
import FlashBanner from '@/components/isinuta/FlashBanner.vue';
import QuickActionCard from '@/components/isinuta/QuickActionCard.vue';
import TwoFactorRequiredBanner from '@/components/isinuta/TwoFactorRequiredBanner.vue';
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
    UserCog,
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
        <div class="flex flex-1 flex-col gap-4">
            <FlashBanner />
            <TwoFactorRequiredBanner />

            <div class="grid gap-4 xl:grid-cols-12 xl:items-stretch">
                <section
                    class="isinuta-hero isinuta-shine relative overflow-hidden rounded-2xl px-4 py-5 text-white md:px-5 md:py-6 xl:col-span-5"
                >
                    <div
                        aria-hidden="true"
                        class="pointer-events-none absolute -right-12 -top-12 size-40 rounded-full bg-white/10 blur-2xl"
                    />
                    <div
                        aria-hidden="true"
                        class="pointer-events-none absolute -bottom-16 -left-8 size-36 rounded-full bg-cyan-300/20 blur-3xl"
                    />
                    <div class="isinuta-water-waves" aria-hidden="true" />
                    <div class="isinuta-water-waves isinuta-water-waves-2" aria-hidden="true" />

                    <div class="relative flex h-full flex-col justify-between gap-4">
                        <div class="space-y-2">
                            <div class="isinuta-hero-badge">
                                <Droplets class="size-3.5" />
                                ISINUTA · Villa Tunari
                            </div>
                            <h2 class="text-xl font-extrabold tracking-tight md:text-2xl">
                                Panel de gestión del agua potable
                            </h2>
                            <p class="text-xs leading-relaxed text-white/85 md:text-sm">
                                Afiliados, cobros, multas y trámites. Tarifa mensual:
                                <strong class="text-white">Bs 23,00</strong>.
                            </p>
                        </div>
                        <Link href="/pagos/crear" class="relative shrink-0 self-start">
                            <Button
                                class="h-9 rounded-lg border-0 bg-white px-4 text-sm font-bold text-cyan-700 shadow-lg shadow-black/10 hover:bg-white/95"
                            >
                                <CreditCard class="mr-1.5 size-4" />
                                Registrar pago
                            </Button>
                        </Link>
                    </div>
                </section>

                <div
                    class="grid grid-cols-2 gap-2.5 sm:grid-cols-3 xl:col-span-7 xl:grid-cols-3 xl:gap-3"
                >
                    <StatCard
                        title="Afiliados activos"
                        :value="totales.afiliados_activos"
                        :icon="Users"
                        variant="info"
                        compact
                    />
                    <StatCard
                        title="Recaudación del mes"
                        :value="formatBs(totales.recaudacion_mes)"
                        :icon="TrendingUp"
                        variant="success"
                        compact
                    />
                    <StatCard
                        title="Deuda pendiente"
                        :value="formatBs(totales.monto_pendiente)"
                        :subtitle="`${totales.pagos_pendientes} períodos sin pagar`"
                        :icon="CreditCard"
                        variant="warning"
                        compact
                    />
                    <StatCard
                        title="Multas pendientes"
                        :value="totales.multas_pendientes"
                        :icon="AlertTriangle"
                        variant="danger"
                        compact
                    />
                    <StatCard
                        title="Trámites pendientes"
                        :value="totales.tramites_pendientes"
                        :icon="ClipboardList"
                        variant="default"
                        compact
                        class="col-span-2 sm:col-span-1"
                    />
                </div>
            </div>

            <section class="space-y-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">
                    Accesos rápidos
                </h3>
                <div class="grid grid-cols-2 gap-2.5 lg:grid-cols-4 lg:gap-3">
                    <QuickActionCard
                        title="Afiliados"
                        description="Consultar titulares"
                        href="/afiliados"
                        :icon="Users"
                        accent="cyan"
                        compact
                    />
                    <QuickActionCard
                        title="Pagos"
                        description="Cobrar cuotas"
                        href="/pagos"
                        :icon="CreditCard"
                        accent="emerald"
                        compact
                    />
                    <QuickActionCard
                        title="Multas"
                        description="Morosidad"
                        href="/multas"
                        :icon="AlertTriangle"
                        accent="amber"
                        compact
                    />
                    <QuickActionCard
                        title="Usuarios"
                        description="Roles y permisos"
                        href="/usuarios"
                        :icon="UserCog"
                        accent="violet"
                        compact
                    />
                </div>
            </section>

            <div class="grid gap-4 lg:grid-cols-2">
                <DataCard
                    title="Recaudación últimos 6 meses"
                    description="Tendencia de ingresos"
                    compact
                >
                    <ul class="space-y-3">
                        <li v-for="item in serie_recaudacion" :key="item.periodo" class="space-y-1.5">
                            <div class="flex justify-between text-sm">
                                <span class="font-medium">{{ item.periodo }}</span>
                                <span class="font-bold text-primary">{{ formatBs(item.monto) }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-muted/80">
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-cyan-400 via-teal-500 to-emerald-500 transition-all duration-700 ease-out"
                                    :style="{
                                        width: `${Math.max(4, (item.monto / maxRecaudacion) * 100)}%`,
                                    }"
                                />
                            </div>
                        </li>
                    </ul>
                    <p
                        v-if="!serie_recaudacion.length"
                        class="py-8 text-center text-sm text-muted-foreground"
                    >
                        Sin datos de recaudación aún.
                    </p>
                </DataCard>

                <DataCard title="Últimos pagos" description="Movimientos recientes" compact>
                    <ul v-if="ultimos_pagos.length" class="isinuta-list">
                        <li
                            v-for="pago in ultimos_pagos"
                            :key="pago.id"
                            class="isinuta-list-item first:pt-0"
                        >
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold">
                                    {{ pago.afiliado.nombres }} {{ pago.afiliado.apellidos }}
                                </p>
                                <p class="text-[11px] text-muted-foreground">
                                    {{ pago.numero_recibo }} · {{ pago.fecha_pago }}
                                </p>
                            </div>
                            <span
                                class="shrink-0 rounded-lg bg-gradient-to-r from-cyan-500/15 to-teal-500/15 px-2.5 py-1 text-xs font-bold text-primary ring-1 ring-primary/20"
                            >
                                {{ formatBs(Number(pago.total)) }}
                            </span>
                        </li>
                    </ul>
                    <p v-else class="py-8 text-center text-sm text-muted-foreground">
                        Aún no hay pagos registrados este período.
                    </p>
                </DataCard>
            </div>
        </div>
    </AppLayout>
</template>
