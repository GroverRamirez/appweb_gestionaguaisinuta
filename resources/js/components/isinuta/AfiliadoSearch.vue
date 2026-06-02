<script setup lang="ts">
import SearchInput from '@/components/isinuta/SearchInput.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { router } from '@inertiajs/vue3';
import { Search, User } from 'lucide-vue-next';
import { ref } from 'vue';

const props = withDefaults(
    defineProps<{
        redirectPath: string;
        searchUrl?: string;
        label?: string;
        hint?: string;
    }>(),
    {
        searchUrl: '/pagos/buscar-afiliado',
        label: 'Buscar afiliado',
        hint: 'Ingrese CI o nombre (mínimo 2 caracteres)',
    },
);

type AfiliadoResult = { id: number; ci: string; nombres: string; apellidos: string };

const busqueda = ref('');
const resultados = ref<AfiliadoResult[]>([]);
const buscando = ref(false);

const buscar = async () => {
    if (busqueda.value.length < 2) {
        return;
    }

    buscando.value = true;
    try {
        const res = await fetch(`${props.searchUrl}?q=${encodeURIComponent(busqueda.value)}`);
        resultados.value = await res.json();
    } finally {
        buscando.value = false;
    }
};

const seleccionar = (afiliado: AfiliadoResult) => {
    router.visit(`${props.redirectPath}?afiliado_id=${afiliado.id}`);
};
</script>

<template>
    <div class="isinuta-card space-y-4 p-6">
        <div>
            <Label class="text-sm font-semibold">{{ label }}</Label>
            <p v-if="hint" class="mt-1 text-xs text-muted-foreground">{{ hint }}</p>
        </div>
        <div class="flex gap-2">
            <SearchInput v-model="busqueda" placeholder="CI o nombre..." class="flex-1" />
            <Button
                type="button"
                class="h-11 shrink-0 rounded-xl px-5 shadow-md shadow-primary/20"
                :disabled="buscando || busqueda.length < 2"
                @click="buscar"
            >
                <Search class="mr-2 size-4" />
                Buscar
            </Button>
        </div>
        <ul v-if="resultados.length" class="isinuta-list overflow-hidden rounded-xl border border-border/60 bg-muted/20">
            <li v-for="a in resultados" :key="a.id">
                <button type="button" class="isinuta-list-item-btn" @click="seleccionar(a)">
                    <div
                        class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-cyan-500 to-teal-600 text-xs font-bold text-white"
                    >
                        <User class="size-3.5" />
                    </div>
                    <div class="min-w-0">
                        <p class="truncate font-semibold leading-tight">{{ a.nombres }} {{ a.apellidos }}</p>
                        <p class="text-[11px] text-muted-foreground">CI {{ a.ci }}</p>
                    </div>
                </button>
            </li>
        </ul>
        <p
            v-else-if="busqueda.length >= 2 && !buscando"
            class="text-center text-sm text-muted-foreground"
        >
            No se encontraron afiliados.
        </p>
    </div>
</template>
