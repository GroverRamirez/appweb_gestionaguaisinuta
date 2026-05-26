<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    BarChart3,
    CreditCard,
    Droplets,
    Shield,
    Users,
    Waves,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { login } from '@/routes';

withDefaults(
    defineProps<{
        sinPermiso?: boolean;
    }>(),
    { sinPermiso: false },
);

const page = usePage();
const user = computed(() => page.props.auth?.user);
const puedeEntrar = computed(() => {
    const auth = page.props.auth as { roles?: string[]; permisos?: string[]; permissions?: string[] };
    const roles = auth?.roles ?? [];
    const permisos = auth?.permisos ?? auth?.permissions ?? [];
    return (
        roles.includes('admin') ||
        roles.includes('cajera') ||
        roles.includes('operador') ||
        permisos.includes('panel.ver')
    );
});

const features = [
    { icon: Users, title: 'Afiliados', desc: 'Registro centralizado sin duplicar cuadernos' },
    { icon: CreditCard, title: 'Pagos', desc: 'Agua Bs 8 + alcantarillado Bs 15 por período' },
    { icon: BarChart3, title: 'Reportes', desc: 'Recaudación y deudas al instante' },
];
</script>

<template>
    <Head title="ISINUTA — Gestión de Agua Potable" />

    <div class="isinuta-mesh relative min-h-svh overflow-hidden">
        <div
            class="pointer-events-none absolute -top-32 left-1/2 size-[600px] -translate-x-1/2 rounded-full bg-cyan-400/20 blur-3xl"
        />
        <div
            aria-hidden="true"
            class="pointer-events-none absolute right-0 bottom-0 size-96 rounded-full bg-teal-500/10 blur-3xl"
        />

        <header class="relative z-10 mx-auto flex max-w-6xl items-center justify-between px-6 py-5">
            <Link href="/" class="flex items-center gap-3">
                <div
                    class="flex size-11 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500 to-teal-600 shadow-lg shadow-cyan-500/30"
                >
                    <Droplets class="size-6 text-white" />
                </div>
                <div>
                    <p class="text-lg font-bold tracking-tight">ISINUTA</p>
                    <p class="text-xs text-muted-foreground">Agua potable · Villa Tunari</p>
                </div>
            </Link>
            <nav class="flex items-center gap-3">
                <template v-if="user && puedeEntrar">
                    <Link href="/panel">
                        <Button class="rounded-xl shadow-md shadow-primary/25">
                            Entrar al panel
                            <ArrowRight class="ml-1 size-4" />
                        </Button>
                    </Link>
                </template>
                <template v-else-if="!user">
                    <Link :href="login()">
                        <Button variant="ghost" class="rounded-xl">Iniciar sesión</Button>
                    </Link>
                </template>
            </nav>
        </header>

        <main class="relative z-10 mx-auto max-w-6xl px-6 pb-20">
            <section class="py-12 text-center md:py-20">
                <div
                    class="mb-6 inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-4 py-1.5 text-xs font-semibold text-primary"
                >
                    <Waves class="size-3.5" />
                    Sistema web administrativo 2026
                </div>
                <h1
                    class="mx-auto max-w-3xl text-4xl font-extrabold tracking-tight text-foreground md:text-6xl md:leading-[1.1]"
                >
                    Gestión moderna del
                    <span class="isinuta-gradient-text">agua potable</span>
                    en Isinuta
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-muted-foreground">
                    Centralice afiliados, cobros, multas, trámites de cambio de nombre y reportes
                    para la Asociación de Agua Potable y Alcantarillado ISINUTA.
                </p>

                <div
                    v-if="sinPermiso"
                    class="mx-auto mt-6 max-w-lg rounded-xl border border-amber-300/80 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-100"
                >
                    Su cuenta no tiene permisos. Use
                    <strong>admin@isinuta.test</strong> o contacte al administrador.
                </div>

                <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                    <Link v-if="!user" :href="login()">
                        <Button size="lg" class="h-12 rounded-xl px-8 text-base shadow-lg shadow-primary/30">
                            Iniciar sesión
                            <ArrowRight class="ml-2 size-5" />
                        </Button>
                    </Link>
                    <Link v-else-if="puedeEntrar" href="/panel">
                        <Button size="lg" class="h-12 rounded-xl px-8 text-base">
                            Ir al panel
                            <ArrowRight class="ml-2 size-5" />
                        </Button>
                    </Link>
                </div>
            </section>

            <section class="grid gap-5 md:grid-cols-3">
                <div
                    v-for="f in features"
                    :key="f.title"
                    class="isinuta-card isinuta-card-hover group p-6"
                >
                    <div
                        class="mb-4 flex size-12 items-center justify-center rounded-xl bg-primary/10 text-primary transition group-hover:scale-110 group-hover:bg-primary group-hover:text-primary-foreground"
                    >
                        <component :is="f.icon" class="size-6" />
                    </div>
                    <h3 class="font-semibold text-foreground">{{ f.title }}</h3>
                    <p class="mt-1 text-sm text-muted-foreground">{{ f.desc }}</p>
                </div>
            </section>

            <section
                class="isinuta-card mt-12 flex flex-col items-center gap-4 p-8 text-center md:flex-row md:text-left"
            >
                <Shield class="size-10 shrink-0 text-primary" />
                <div class="flex-1">
                    <h2 class="text-lg font-semibold">Seguro y multiusuario</h2>
                    <p class="text-sm text-muted-foreground">
                        Roles de administrador y operador. Acceso desde cualquier navegador con
                        respaldo en base de datos.
                    </p>
                </div>
            </section>
        </main>

        <footer class="relative z-10 border-t border-border/60 py-6 text-center text-xs text-muted-foreground">
            Asociación de Agua Potable y Alcantarillado Isinuta · Eterazama — Bolivia
        </footer>
    </div>
</template>
