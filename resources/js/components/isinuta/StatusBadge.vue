<script setup lang="ts">

import { computed } from 'vue';

import { etiquetaDe, etiquetaEstado } from '@/lib/etiquetas';



const props = defineProps<{

    status: string;

    label?: string;

}>();



const texto = computed(() => props.label ?? etiquetaDe(props.status, etiquetaEstado));



const classes = computed(() => {

    const s = props.status.toLowerCase();

    if (['activo', 'pagado', 'aprobado', 'pagada', 'confirmado'].includes(s)) {

        return 'bg-emerald-500/15 text-emerald-700 ring-emerald-500/20 dark:text-emerald-300';

    }

    if (['pendiente'].includes(s)) {

        return 'bg-amber-500/15 text-amber-800 ring-amber-500/20 dark:text-amber-300';

    }

    if (['inactivo', 'rechazado', 'anulado'].includes(s)) {

        return 'bg-red-500/15 text-red-700 ring-red-500/20 dark:text-red-300';

    }

    return 'bg-muted text-muted-foreground ring-border';

});

</script>



<template>

    <span

        class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold ring-1 ring-inset"

        :class="classes"

    >

        {{ texto }}

    </span>

</template>

