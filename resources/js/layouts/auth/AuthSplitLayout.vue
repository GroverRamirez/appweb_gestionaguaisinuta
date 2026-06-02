<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Droplets, MapPin, Shield } from 'lucide-vue-next';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative grid min-h-svh lg:grid-cols-2">
        <div
            class="isinuta-auth-panel relative hidden flex-col justify-between overflow-hidden p-10 text-white lg:flex"
        >
            <div
                class="pointer-events-none absolute inset-0 opacity-40"
                style="
                    background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.08\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
                "
            />
            <div
                class="pointer-events-none absolute -right-20 -top-20 size-80 rounded-full bg-cyan-400/20 blur-3xl"
            />
            <div
                aria-hidden="true"
                class="pointer-events-none absolute -bottom-16 -left-16 size-72 rounded-full bg-teal-300/15 blur-3xl"
            />

            <Link :href="home()" class="relative z-10 flex items-center gap-3">
                <div
                    class="flex size-11 items-center justify-center rounded-xl bg-white/15 backdrop-blur-sm ring-1 ring-white/20"
                >
                    <Droplets class="size-6 text-cyan-200" />
                </div>
                <div>
                    <p class="text-lg font-bold tracking-tight">ISINUTA</p>
                    <p class="text-xs text-white/70">Gestión de agua potable</p>
                </div>
            </Link>

            <div class="relative z-10 max-w-md space-y-6">
                <h2 class="text-3xl font-bold leading-tight tracking-tight">
                    Administración inteligente del servicio de agua
                </h2>
                <p class="text-sm leading-relaxed text-white/80">
                    Afiliados, cobros mensuales, multas, trámites y reportes en una sola
                    plataforma para la Asociación de Agua Potable y Alcantarillado Isinuta.
                </p>
                <ul class="space-y-3 text-sm text-white/75">
                    <li class="flex items-center gap-2">
                        <Shield class="size-4 text-cyan-300" />
                        Acceso seguro por roles
                    </li>
                    <li class="flex items-center gap-2">
                        <MapPin class="size-4 text-cyan-300" />
                        Villa Tunari — Cochabamba, Bolivia
                    </li>
                </ul>
            </div>

            <p class="relative z-10 text-xs text-white/50">
                © {{ new Date().getFullYear() }} Asociación ISINUTA
            </p>
            <div class="isinuta-water-waves" aria-hidden="true" />
            <div class="isinuta-water-waves isinuta-water-waves-2" aria-hidden="true" />
        </div>

        <div class="isinuta-mesh flex flex-col items-center justify-center p-6 md:p-10">
            <div
                class="w-full max-w-[420px] space-y-8 rounded-3xl border border-border/50 bg-card/80 p-8 shadow-2xl shadow-cyan-900/10 backdrop-blur-xl md:p-10"
            >
                <div class="flex flex-col items-center gap-2 text-center lg:items-start lg:text-left">
                    <Link :href="home()" class="mb-2 flex items-center gap-2 lg:hidden">
                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500 to-teal-600"
                        >
                            <Droplets class="size-5 text-white" />
                        </div>
                        <span class="font-bold">ISINUTA</span>
                    </Link>
                    <h1 v-if="title" class="text-2xl font-bold tracking-tight">
                        {{ title }}
                    </h1>
                    <p v-if="description" class="text-sm text-muted-foreground">
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
