<script setup lang="ts">
import { Droplets } from 'lucide-vue-next';
</script>

<template>
    <div
        class="flex aspect-square size-9 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-400 to-teal-600 shadow-lg shadow-cyan-500/25"
    >
        <Droplets class="size-5 text-white" />
    </div>
    <div class="ml-2 grid flex-1 text-left leading-tight group-data-[collapsible=icon]:hidden">
        <span class="truncate text-sm font-bold tracking-tight text-sidebar-foreground">ISINUTA</span>
        <span class="truncate text-[10px] font-medium text-sidebar-foreground/60">
            Agua potable
        </span>
    </div>
</template>
