<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    AlertTriangle,
    ArrowRight,
    BarChart3,
    ClipboardList,
    CreditCard,
    Droplets,
    LayoutGrid,
    Sparkles,
    UserCog,
    Users,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { login } from '@/routes';

type VistaPrevia = {
    afiliados_activos: number;
    recaudacion_mes: number;
    deuda_pendiente: number;
    tramites_pendientes: number;
};

const props = withDefaults(
    defineProps<{
        sinPermiso?: boolean;
        vistaPrevia?: VistaPrevia;
    }>(),
    {
        sinPermiso: false,
        vistaPrevia: () => ({
            afiliados_activos: 0,
            recaudacion_mes: 0,
            deuda_pendiente: 0,
            tramites_pendientes: 0,
        }),
    },
);

const page = usePage();
const user = computed(() => page.props.auth?.user);
const puedeEntrar = computed(() => {
    const auth = page.props.auth as {
        roles?: string[];
        permisos?: string[];
        permissions?: string[];
    };
    const roles = auth?.roles ?? [];
    const permisos = auth?.permisos ?? auth?.permissions ?? [];

    return (
        roles.includes('admin') ||
        roles.includes('cajera') ||
        roles.includes('operador') ||
        permisos.includes('panel.ver')
    );
});

const formatoBs = (valor: number) =>
    new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
        minimumFractionDigits: 2,
    }).format(valor);

const previewCards = computed(() => [
    {
        label: 'Afiliados activos',
        value: String(props.vistaPrevia.afiliados_activos),
    },
    {
        label: 'Recaudación mes',
        value: formatoBs(props.vistaPrevia.recaudacion_mes),
    },
    {
        label: 'Deuda pendiente',
        value: formatoBs(props.vistaPrevia.deuda_pendiente),
    },
    {
        label: 'Trámites pendientes',
        value: String(props.vistaPrevia.tramites_pendientes),
    },
]);

const modulos = [
    {
        icon: Users,
        title: 'Afiliados',
        desc: 'Registro y consulta de titulares del servicio.',
        accent: 'from-sky-400 to-cyan-500',
        glow: 'bg-sky-400',
    },
    {
        icon: CreditCard,
        title: 'Pagos',
        desc: 'Cobro de la cuota mensual (Bs 23,00) con recibo automático.',
        accent: 'from-emerald-400 to-teal-500',
        glow: 'bg-emerald-400',
    },
    {
        icon: AlertTriangle,
        title: 'Multas',
        desc: 'Control de morosidad y sanciones por atraso.',
        accent: 'from-amber-400 to-orange-500',
        glow: 'bg-amber-400',
    },
    {
        icon: ClipboardList,
        title: 'Trámites',
        desc: 'Cambio de titular con verificación de deudas.',
        accent: 'from-blue-400 to-indigo-500',
        glow: 'bg-blue-400',
    },
    {
        icon: BarChart3,
        title: 'Reportes',
        desc: 'Recaudación, deudas y listados consolidados.',
        accent: 'from-violet-400 to-purple-500',
        glow: 'bg-violet-400',
    },
    {
        icon: UserCog,
        title: 'Usuarios y seguridad',
        desc: 'Roles (admin, cajera, operador), permisos y auditoría de cuentas.',
        accent: 'from-rose-400 to-pink-500',
        glow: 'bg-rose-400',
    },
];
</script>

<template>
    <Head title="ISINUTA — Gestión de Agua Potable" />

    <div class="isinuta-mesh relative flex min-h-svh flex-col">
        <header class="isinuta-welcome-header">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
                <Link href="/" class="group flex items-center gap-3">
                    <div
                        class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500 to-teal-600 shadow-lg shadow-cyan-500/30 ring-2 ring-white/50 transition group-hover:scale-105"
                    >
                        <Droplets class="size-5 text-white" />
                    </div>
                    <div>
                        <p class="text-lg font-extrabold tracking-tight text-foreground">ISINUTA</p>
                        <p class="text-[11px] font-medium text-muted-foreground">
                            Agua potable · Villa Tunari
                        </p>
                    </div>
                </Link>
                <nav class="flex shrink-0 items-center gap-2">
                    <template v-if="user && puedeEntrar">
                        <Link href="/panel">
                            <Button
                                size="sm"
                                class="rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-4 shadow-md shadow-cyan-600/30"
                            >
                                <LayoutGrid class="mr-1.5 size-3.5" />
                                Entrar al panel
                            </Button>
                        </Link>
                    </template>
                    <template v-else-if="!user">
                        <Link :href="login()">
                            <Button
                                size="sm"
                                class="rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-4 shadow-md shadow-cyan-600/30"
                            >
                                Iniciar sesión
                            </Button>
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <section class="relative overflow-hidden pb-8 sm:pb-10">
            <div
                class="isinuta-welcome-hero isinuta-shine relative px-4 pb-16 pt-8 text-white sm:px-6 sm:pb-20 sm:pt-10 lg:pt-12"
            >
                <div class="isinuta-welcome-hero-grid pointer-events-none absolute inset-0" aria-hidden="true" />
                <div
                    class="pointer-events-none absolute -right-24 -top-24 size-80 rounded-full bg-white/10 blur-3xl"
                />
                <div class="isinuta-water-waves" aria-hidden="true" />
                <div class="isinuta-water-waves isinuta-water-waves-2" aria-hidden="true" />
                <div class="isinuta-welcome-hero-curve" aria-hidden="true" />

                <div class="relative mx-auto max-w-6xl">
                    <div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:gap-12">
                        <div class="text-center lg:text-left">
                            <div
                                class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-4 py-1.5 text-[11px] font-bold tracking-wide backdrop-blur-md"
                            >
                                <Sparkles class="size-3.5 text-amber-200" />
                                Plataforma administrativa · 2026
                            </div>
                            <h1
                                class="isinuta-animate-fade-up text-[1.75rem] font-extrabold leading-[1.15] tracking-tight sm:text-4xl lg:text-5xl"
                            >
                                Gestión moderna del
                                <span class="text-cyan-100">agua potable</span>
                                en Isinuta
                            </h1>
                            <p
                                class="mx-auto mt-5 max-w-xl text-sm leading-relaxed text-white/90 sm:text-base lg:mx-0"
                            >
                                Sistema interno de la Asociación de Agua Potable y Alcantarillado
                                ISINUTA — Villa Tunari, Eterazama.
                            </p>

                            <div
                                v-if="sinPermiso"
                                class="mx-auto mt-6 max-w-lg rounded-2xl border border-amber-200/40 bg-amber-400/20 px-4 py-3 text-left text-xs text-white backdrop-blur-sm lg:mx-0"
                            >
                                <p class="font-semibold">Sin acceso al panel</p>
                                <p class="mt-1 text-white/85">
                                    Su cuenta no tiene permisos. Contacte al administrador del
                                    sistema.
                                </p>
                            </div>

                            <div
                                class="mt-8 flex flex-wrap items-center justify-center lg:justify-start"
                            >
                                <Link v-if="!user" :href="login()">
                                    <Button
                                        size="lg"
                                        class="h-12 rounded-2xl border-0 bg-white px-8 text-base font-bold text-cyan-800 shadow-xl shadow-black/20 transition hover:bg-white/95"
                                    >
                                        Iniciar sesión
                                        <ArrowRight class="ml-2 size-4" />
                                    </Button>
                                </Link>
                                <Link v-else-if="puedeEntrar" href="/panel">
                                    <Button
                                        size="lg"
                                        class="h-12 rounded-2xl border-0 bg-white px-8 text-base font-bold text-cyan-800 shadow-xl shadow-black/20 transition hover:bg-white/95"
                                    >
                                        Ir al panel
                                        <ArrowRight class="ml-2 size-4" />
                                    </Button>
                                </Link>
                            </div>
                        </div>

                        <div class="isinuta-animate-float hidden md:block">
                            <div class="isinuta-welcome-preview space-y-3.5 p-5">
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="flex size-10 items-center justify-center rounded-xl bg-white/20 ring-1 ring-white/30"
                                    >
                                        <LayoutGrid class="size-5" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold">Panel ISINUTA</p>
                                        <p class="text-[11px] text-white/65">Indicadores actuales</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <div
                                        v-for="card in previewCards"
                                        :key="card.label"
                                        class="rounded-xl bg-white/12 p-3 ring-1 ring-white/15"
                                    >
                                        <p class="text-[10px] font-medium text-white/60">
                                            {{ card.label }}
                                        </p>
                                        <p class="mt-1 truncate text-lg font-bold tabular-nums">
                                            {{ card.value }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="isinuta-welcome-preview-mobile mt-8 md:hidden">
                        <div class="grid grid-cols-2 gap-2">
                            <div
                                v-for="card in previewCards"
                                :key="card.label"
                                class="rounded-xl bg-white/12 p-2.5 text-center ring-1 ring-white/15"
                            >
                                <p class="text-[9px] text-white/60">{{ card.label }}</p>
                                <p class="mt-0.5 truncate text-sm font-bold tabular-nums">
                                    {{ card.value }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <main class="relative z-10 mx-auto w-full max-w-6xl flex-1 px-4 py-2 sm:px-6 sm:py-6">
            <div class="mb-6 text-center sm:mb-8">
                <h2 class="text-2xl font-extrabold tracking-tight text-foreground sm:text-3xl">
                    Módulos del sistema
                </h2>
            </div>

            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="(modulo, i) in modulos"
                    :key="modulo.title"
                    class="group isinuta-welcome-module isinuta-animate-fade-up"
                    :style="{ animationDelay: `${i * 0.06}s` }"
                >
                    <div class="isinuta-welcome-module-glow" :class="modulo.glow" />
                    <div
                        class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r opacity-80"
                        :class="modulo.accent"
                    />
                    <div class="relative flex items-start gap-4">
                        <div
                            class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br text-white shadow-lg transition duration-300 group-hover:scale-110"
                            :class="modulo.accent"
                        >
                            <component :is="modulo.icon" class="size-5" />
                        </div>
                        <div class="min-w-0 pt-0.5">
                            <h3 class="text-base font-bold text-foreground">{{ modulo.title }}</h3>
                            <p class="mt-1.5 text-sm leading-relaxed text-muted-foreground">
                                {{ modulo.desc }}
                            </p>
                        </div>
                    </div>
                </article>
            </section>
        </main>

        <footer class="relative z-10 mt-6 border-t border-border/60 bg-card/50 py-8 backdrop-blur-sm">
            <div
                class="mx-auto flex max-w-6xl flex-col items-center gap-3 px-4 text-center sm:px-6"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500 to-teal-600"
                    >
                        <Droplets class="size-4 text-white" />
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-bold text-foreground">
                            Asociación de Agua Potable y Alcantarillado Isinuta
                        </p>
                        <p class="text-xs text-muted-foreground">
                            Eterazama · Cochabamba · Bolivia
                        </p>
                    </div>
                </div>
                <p class="text-[11px] text-muted-foreground">
                    Sistema interno · Solo personal autorizado
                </p>
            </div>
        </footer>
    </div>
</template>
