<script setup lang="ts">
const model = defineModel<string>({ default: '' });

defineProps<{
    label?: string;
}>();
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <label v-if="label" class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
            {{ label }}
        </label>
        <select v-model="model" class="isinuta-select min-w-[140px]">
            <slot />
        </select>
    </div>
</template>
