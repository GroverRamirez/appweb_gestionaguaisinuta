<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { InertiaForm } from '@inertiajs/vue3';

type AfiliadoFormData = {
    ci: string;
    nombres: string;
    apellidos: string;
    telefono: string;
    direccion: string;
    fecha_afiliacion: string;
    estado: string;
};

const props = defineProps<{
    form: InertiaForm<AfiliadoFormData>;
    submitLabel: string;
    showDelete?: boolean;
}>();

const emit = defineEmits<{
    submit: [];
    delete: [];
}>();
</script>

<template>
    <form class="space-y-6" @submit.prevent="emit('submit')">
        <div class="grid gap-6 md:grid-cols-2">
            <div class="grid gap-2">
                <Label for="ci">Cédula de identidad (CI)</Label>
                <Input
                    id="ci"
                    v-model="form.ci"
                    class="h-11 rounded-xl"
                    placeholder="Ej. 1234567"
                    required
                />
                <InputError :message="form.errors.ci" />
            </div>

            <div class="grid gap-2">
                <Label for="fecha_afiliacion">Fecha de afiliación</Label>
                <Input
                    id="fecha_afiliacion"
                    v-model="form.fecha_afiliacion"
                    type="date"
                    class="h-11 rounded-xl"
                />
                <InputError :message="form.errors.fecha_afiliacion" />
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="grid gap-2">
                <Label for="nombres">Nombres</Label>
                <Input
                    id="nombres"
                    v-model="form.nombres"
                    class="h-11 rounded-xl"
                    placeholder="Nombres del afiliado"
                    required
                />
                <InputError :message="form.errors.nombres" />
            </div>

            <div class="grid gap-2">
                <Label for="apellidos">Apellidos</Label>
                <Input
                    id="apellidos"
                    v-model="form.apellidos"
                    class="h-11 rounded-xl"
                    placeholder="Apellidos del afiliado"
                    required
                />
                <InputError :message="form.errors.apellidos" />
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="grid gap-2">
                <Label for="telefono">Teléfono / celular</Label>
                <Input
                    id="telefono"
                    v-model="form.telefono"
                    class="h-11 rounded-xl"
                    placeholder="Ej. 70012345"
                />
                <InputError :message="form.errors.telefono" />
            </div>

            <div class="grid gap-2">
                <Label for="estado">Estado</Label>
                <select
                    id="estado"
                    v-model="form.estado"
                    class="isinuta-input"
                >
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
                <InputError :message="form.errors.estado" />
            </div>
        </div>

        <div class="grid gap-2">
            <Label for="direccion">Dirección</Label>
            <Input
                id="direccion"
                v-model="form.direccion"
                class="h-11 rounded-xl"
                placeholder="Ej. Zona Central, Calle A..."
            />
            <InputError :message="form.errors.direccion" />
        </div>

        <div
            class="flex flex-col-reverse gap-3 border-t border-border/60 pt-6 sm:flex-row sm:items-center"
            :class="showDelete ? 'sm:justify-between' : 'sm:justify-end'"
        >
            <Button
                v-if="showDelete"
                type="button"
                variant="destructive"
                class="rounded-xl"
                :disabled="form.processing"
                @click="emit('delete')"
            >
                Eliminar afiliado
            </Button>
            <Button type="submit" class="rounded-xl shadow-md shadow-primary/20" :disabled="form.processing">
                {{ submitLabel }}
            </Button>
        </div>
    </form>
</template>
