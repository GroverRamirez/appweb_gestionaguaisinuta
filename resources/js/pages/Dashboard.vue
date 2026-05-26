<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onMounted } from 'vue';

onMounted(() => {
    router.visit('/panel', { replace: true });
});
</script>

<template>
    <div />
</template>
