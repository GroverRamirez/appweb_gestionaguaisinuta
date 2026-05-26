<script setup lang="ts">
import type { Component } from 'vue';
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        title: string;
        value: string | number;
        subtitle?: string;
        icon: Component;
        trend?: string;
        variant?: 'default' | 'success' | 'warning' | 'danger' | 'info';
    }>(),
    { variant: 'default' },
);

const accentBar = computed(() => {
    const map = {
        default: 'isinuta-stat-accent-default',
        success: 'isinuta-stat-accent-success',
        warning: 'isinuta-stat-accent-warning',
        danger: 'isinuta-stat-accent-danger',
        info: 'isinuta-stat-accent-info',
    };
    return map[props.variant];
});

const iconClass = computed(() => {
    const map = {
        default: 'bg-gradient-to-br from-cyan-500 to-teal-600 text-white shadow-cyan-500/30',
        success: 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-emerald-500/30',
        warning: 'bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-amber-500/30',
        danger: 'bg-gradient-to-br from-rose-500 to-red-600 text-white shadow-rose-500/30',
        info: 'bg-gradient-to-br from-sky-500 to-cyan-600 text-white shadow-sky-500/30',
    };
    return map[props.variant];
});
</script>

<template>
    <div
        class="isinuta-card isinuta-card-hover isinuta-shine group relative overflow-hidden p-5"
    >
        <div
            aria-hidden="true"
            class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r opacity-90"
            :class="accentBar"
        />
        <div
            aria-hidden="true"
            class="pointer-events-none absolute -right-8 -top-8 size-32 rounded-full bg-gradient-to-br from-primary/10 to-transparent blur-2xl transition group-hover:from-primary/20"
        />
        <div class="relative flex items-start justify-between gap-3 pt-1">
            <div class="min-w-0 flex-1 space-y-2">
                <p class="text-xs font-bold uppercase tracking-wider text-muted-foreground">
                    {{ title }}
                </p>
                <p class="text-2xl font-extrabold tracking-tight text-foreground sm:text-3xl">
                    {{ value }}
                </p>
                <p v-if="subtitle" class="text-xs font-medium text-muted-foreground">
                    {{ subtitle }}
                </p>
                <p v-if="trend" class="text-xs font-semibold text-primary">{{ trend }}</p>
            </div>
            <div
                class="flex size-14 shrink-0 items-center justify-center rounded-2xl shadow-lg transition group-hover:scale-110 group-hover:shadow-xl"
                :class="iconClass"
            >
                <component :is="icon" class="size-6" />
            </div>
        </div>
    </div>
</template>
