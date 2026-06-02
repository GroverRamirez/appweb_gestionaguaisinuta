<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ShieldAlert, X } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import type { Auth } from '@/types';

const STORAGE_KEY = 'isinuta_aviso_2fa_oculto';

const page = usePage();
const auth = computed(() => page.props.auth as Auth);
const ocultoLocal = ref(false);

onMounted(() => {
    ocultoLocal.value = localStorage.getItem(STORAGE_KEY) === '1';
});

const mostrar = computed(
    () => auth.value.requires_two_factor_setup === true && !ocultoLocal.value,
);

function ocultarAviso(): void {
    localStorage.setItem(STORAGE_KEY, '1');
    ocultoLocal.value = true;
}
</script>

<template>
    <div
        v-if="mostrar"
        class="flex flex-col gap-3 rounded-xl border border-amber-300/80 bg-amber-50 px-4 py-3 text-sm text-amber-950 sm:flex-row sm:items-center sm:justify-between dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-100"
        role="alert"
    >
        <div class="flex items-start gap-3">
            <ShieldAlert class="mt-0.5 size-5 shrink-0 text-amber-600 dark:text-amber-400" />
            <div>
                <p class="font-semibold">Active la verificación en dos pasos</p>
                <p class="mt-0.5 text-xs leading-relaxed opacity-90">
                    Como administrador, se recomienda activar 2FA en
                    <Link href="/settings/security" class="font-semibold underline underline-offset-2">
                        Configuración → Seguridad
                    </Link>.
                </p>
            </div>
        </div>
        <div class="flex shrink-0 flex-wrap items-center gap-2">
            <Link href="/settings/security">
                <Button size="sm" class="rounded-lg">
                    Configurar 2FA
                </Button>
            </Link>
            <Button
                type="button"
                size="sm"
                variant="ghost"
                class="rounded-lg text-amber-900 hover:bg-amber-100/80 dark:text-amber-100 dark:hover:bg-amber-900/40"
                @click="ocultarAviso"
            >
                <X class="mr-1 size-3.5" />
                Ocultar aviso
            </Button>
        </div>
    </div>
</template>
