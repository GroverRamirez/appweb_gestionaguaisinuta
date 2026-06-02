<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Component } from 'vue';

withDefaults(
    defineProps<{
        title: string;
        description: string;
        href: string;
        icon: Component;
        accent?: 'cyan' | 'emerald' | 'amber' | 'violet';
        compact?: boolean;
    }>(),
    { compact: false },
);

const accentMap = {
    cyan: 'from-sky-500 to-cyan-600 shadow-sky-500/25',
    emerald: 'from-emerald-500 to-teal-600 shadow-emerald-500/25',
    amber: 'from-amber-400 to-orange-500 shadow-amber-500/25',
    violet: 'from-violet-500 to-purple-600 shadow-violet-500/25',
};
</script>

<template>
    <Link
        :href="href"
        class="isinuta-quick-action group"
        :class="compact ? 'isinuta-quick-action-compact' : ''"
    >
        <div
            class="flex shrink-0 items-center justify-center rounded-xl bg-gradient-to-br text-white shadow-lg transition group-hover:scale-105"
            :class="[accentMap[accent ?? 'cyan'], compact ? 'size-9' : 'size-12 rounded-2xl group-hover:scale-110']"
        >
            <component :is="icon" :class="compact ? 'size-4' : 'size-5'" />
        </div>
        <div class="min-w-0">
            <p class="font-bold text-foreground" :class="compact ? 'text-sm' : ''">{{ title }}</p>
            <p
                class="text-muted-foreground"
                :class="compact ? 'truncate text-[11px]' : 'mt-0.5 text-xs leading-relaxed'"
            >
                {{ description }}
            </p>
        </div>
    </Link>
</template>
