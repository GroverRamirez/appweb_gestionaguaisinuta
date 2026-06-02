<script setup lang="ts">
withDefaults(
    defineProps<{
        title?: string;
        description?: string;
        compact?: boolean;
    }>(),
    { compact: false },
);
</script>

<template>
    <div class="isinuta-card overflow-hidden ring-1 ring-cyan-500/10">
        <div
            v-if="title || $slots.header"
            class="isinuta-data-card-header flex items-center justify-between"
            :class="compact ? 'px-4 py-2.5' : 'px-5 py-4'"
        >
            <div v-if="title">
                <h2 class="font-semibold text-foreground">{{ title }}</h2>
                <p v-if="description" class="mt-0.5 text-xs text-muted-foreground">
                    {{ description }}
                </p>
            </div>
            <slot name="header" />
        </div>
        <div class="bg-card/50" :class="compact ? 'p-3.5' : 'p-5'">
            <slot />
        </div>
    </div>
</template>
