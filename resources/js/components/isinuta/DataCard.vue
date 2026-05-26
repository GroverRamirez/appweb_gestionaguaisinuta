<script setup lang="ts">
defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="isinuta-card overflow-hidden">
        <div
            v-if="title || $slots.header"
            class="isinuta-data-card-header flex items-center justify-between px-5 py-4"
        >
            <div v-if="title">
                <h2 class="font-semibold text-foreground">{{ title }}</h2>
                <p v-if="description" class="mt-0.5 text-xs text-muted-foreground">
                    {{ description }}
                </p>
            </div>
            <slot name="header" />
        </div>
        <div class="bg-card/50 p-5">
            <slot />
        </div>
    </div>
</template>
