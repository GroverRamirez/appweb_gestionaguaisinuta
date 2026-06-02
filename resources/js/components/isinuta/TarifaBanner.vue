<script setup lang="ts">
import { Droplets } from 'lucide-vue-next';
</script>

<template>
    <div
        class="flex flex-wrap items-center gap-4 rounded-2xl border border-cyan-200/60 bg-gradient-to-r from-cyan-50/90 via-white to-teal-50/80 p-4 shadow-sm dark:border-cyan-800/40 dark:from-cyan-950/40 dark:via-transparent dark:to-teal-950/30"
    >
        <div
            class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500 to-teal-600 text-white shadow-lg shadow-cyan-500/30"
        >
            <Droplets class="size-6" />
        </div>
        <div class="flex-1 min-w-[200px]">
            <p class="text-xs font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-300">
                Tarifa mensual ISINUTA
            </p>
            <p class="mt-0.5 text-sm text-muted-foreground">
                Agua potable <strong class="text-foreground">Bs 8,00</strong>
                + Alcantarillado <strong class="text-foreground">Bs 15,00</strong>
            </p>
        </div>
        <div
            class="rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 px-5 py-2.5 text-center text-white shadow-md shadow-cyan-500/25"
        >
            <p class="text-[10px] font-semibold uppercase tracking-widest text-white/80">Total</p>
            <p class="text-xl font-extrabold leading-none">Bs 23,00</p>
        </div>
    </div>
</template>
