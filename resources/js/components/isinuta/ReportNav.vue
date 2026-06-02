<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useCurrentUrl } from '@/composables/useCurrentUrl';

const tabs = [
    { label: 'Recaudación', href: '/reportes/recaudacion' },
    { label: 'Deudas pendientes', href: '/reportes/deudas' },
    { label: 'Listado afiliados', href: '/reportes/afiliados' },
];

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <nav class="isinuta-report-tabs" aria-label="Reportes">
        <Link
            v-for="tab in tabs"
            :key="tab.href"
            :href="tab.href"
            class="isinuta-report-tab"
            :class="{ 'isinuta-report-tab-active': isCurrentUrl(tab.href) }"
        >
            {{ tab.label }}
        </Link>
    </nav>
</template>
