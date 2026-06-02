<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Droplets } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Iniciar sesión',
        description: 'Ingrese su correo y contraseña para acceder al sistema ISINUTA',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Iniciar sesión" />

    <div
        v-if="status"
        class="isinuta-alert-success mb-4 text-center"
    >
        {{ status }}
    </div>

    <div
        class="mb-2 flex items-center justify-center gap-2 text-primary lg:hidden"
    >
        <Droplets class="size-5" />
        <span class="text-sm font-bold">ISINUTA</span>
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-5">
            <div class="grid gap-2">
                <Label for="email">Correo electrónico</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="admin@isinuta.test"
                    class="isinuta-input h-11"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password">Contraseña</Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-sm"
                        :tabindex="5"
                    >
                        ¿Olvidó su contraseña?
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="rounded-xl"
                />
                <InputError :message="errors.password" />
            </div>

            <Label for="remember" class="flex items-center gap-3">
                <Checkbox id="remember" name="remember" :tabindex="3" />
                <span class="text-sm">Recordarme en este equipo</span>
            </Label>

            <Button
                type="submit"
                class="mt-2 h-12 w-full rounded-xl bg-gradient-to-r from-cyan-600 to-teal-600 text-base font-bold shadow-xl shadow-cyan-500/30 hover:from-cyan-500 hover:to-teal-500"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                Ingresar al sistema
            </Button>
        </div>

    </Form>
</template>
