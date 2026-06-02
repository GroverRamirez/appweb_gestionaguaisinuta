<script setup lang="ts">
import type { Component } from 'vue';

withDefaults(
    defineProps<{
        title: string;
        description?: string;
        icon?: Component;
    }>(),
    {},
);
</script>

<template>
    <div
        class="isinuta-animate-fade-up flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
    >
        <div class="isinuta-page-header-accent space-y-2">
            <div v-if="icon" class="mb-1 flex items-center gap-3">
                <div
                    class="flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500 to-teal-600 text-white shadow-lg shadow-cyan-500/30"
                >
                    <component :is="icon" class="size-5" />
                </div>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-foreground sm:text-3xl">
                {{ title }}
            </h1>
            <p
                v-if="description"
                class="max-w-2xl text-sm leading-relaxed text-muted-foreground"
            >
                {{ description }}
            </p>
        </div>
        <div v-if="$slots.actions" class="flex shrink-0 flex-wrap items-center gap-2">
            <slot name="actions" />
        </div>
    </div>
</template>
