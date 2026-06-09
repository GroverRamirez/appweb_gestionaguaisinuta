<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { CheckCircle2, AlertCircle, Info } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();
const flash = computed(() => (page.props.flash as Record<string, string | undefined>) ?? {});

const message = computed(() => flash.value.success ?? flash.value.error ?? flash.value.info);
const type = computed(() => {
    if (flash.value.success) {
        return 'success';
    }

    if (flash.value.error) {
        return 'error';
    }

    return 'info';
});
</script>

<template>
    <div
        v-if="message"
        class="flex items-center gap-3 rounded-xl border px-4 py-3 text-sm font-medium shadow-sm"
        :class="{
            'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-100':
                type === 'success',
            'border-red-200 bg-red-50 text-red-900 dark:border-red-800 dark:bg-red-950/50 dark:text-red-100':
                type === 'error',
            'border-sky-200 bg-sky-50 text-sky-900 dark:border-sky-800 dark:bg-sky-950/50 dark:text-sky-100':
                type === 'info',
        }"
    >
        <CheckCircle2 v-if="type === 'success'" class="size-5 shrink-0" />
        <AlertCircle v-else-if="type === 'error'" class="size-5 shrink-0" />
        <Info v-else class="size-5 shrink-0" />
        <span>{{ message }}</span>
    </div>
</template>
