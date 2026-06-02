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
        compact?: boolean;
    }>(),
    { variant: 'default', compact: false },
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
        class="isinuta-card isinuta-card-hover isinuta-shine group relative overflow-hidden"
        :class="compact ? 'p-3.5' : 'p-5'"
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
        <div class="relative flex items-start justify-between gap-2 pt-0.5">
            <div class="min-w-0 flex-1" :class="compact ? 'space-y-0.5' : 'space-y-2'">
                <p class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground">
                    {{ title }}
                </p>
                <p
                    class="font-extrabold tracking-tight text-foreground"
                    :class="compact ? 'text-xl' : 'text-2xl sm:text-3xl'"
                >
                    {{ value }}
                </p>
                <p v-if="subtitle" class="text-xs font-medium text-muted-foreground">
                    {{ subtitle }}
                </p>
                <p v-if="trend" class="text-xs font-semibold text-primary">{{ trend }}</p>
            </div>
            <div
                class="flex shrink-0 items-center justify-center rounded-xl shadow-lg transition group-hover:scale-105 group-hover:shadow-xl"
                :class="[iconClass, compact ? 'size-10' : 'size-14 rounded-2xl group-hover:scale-110']"
            >
                <component :is="icon" :class="compact ? 'size-4' : 'size-6'" />
            </div>
        </div>
    </div>
</template>
