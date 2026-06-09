<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    links: Array<{ url: string | null; label: string; active: boolean }>;
}>();

const labelText = (label: string) => label.replace(/&laquo;\s*/g, '').replace(/\s*&raquo;/g, '');
</script>

<template>
    <nav v-if="links.length > 3" class="flex flex-wrap justify-center gap-1">
        <template v-for="(link, key) in links" :key="key">
            <span
                v-if="link.url === null"
                class="rounded-lg border border-transparent px-3 py-2 text-sm text-muted-foreground"
            >
                {{ labelText(link.label) }}
            </span>
            <Link
                v-else
                :href="link.url"
                class="rounded-lg border px-3 py-2 text-sm transition"
                :class="
                    link.active
                        ? 'border-primary bg-primary text-primary-foreground shadow-sm'
                        : 'border-border hover:bg-accent'
                "
            >
                {{ labelText(link.label) }}
            </Link>
        </template>
    </nav>
</template>
