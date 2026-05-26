<script setup lang="ts">
import { Search } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';

const model = defineModel<string>({ default: '' });

withDefaults(
    defineProps<{
        placeholder?: string;
        class?: string;
    }>(),
    {
        placeholder: 'Buscar...',
    },
);
</script>

<template>
    <div class="relative w-full" :class="$props.class">
        <Search
            class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-muted-foreground"
        />
        <Input
            v-model="model"
            type="search"
            class="h-11 w-full rounded-xl border-border/80 bg-card pl-10 shadow-sm ring-primary/20 transition focus-visible:ring-2"
            :placeholder="placeholder"
        />
    </div>
</template>
