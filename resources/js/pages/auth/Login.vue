<script setup lang="ts">
import TextLink from '@/components/shared/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout/AuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { h, VNode } from 'vue';
import FormInput from '@/components/form-elements/FormInput.vue';

type H = typeof h;

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

defineOptions({
    layout: (h: H, page: VNode): VNode =>
        h(
            AuthLayout,
            {
                title: 'Вход на сайт',
                description: 'Введите email и пароль, чтобы войти',
            },
            () => page,
        ),
});

</script>

<template>
    <Head title="Вход на сайт" />

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ status }}
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <FormInput
                field-name="email"
                type="email"
                placeholder="ваш email"
                :error-message="errors.email"
            />

            <FormInput
                field-name="password"
                type="password"
                placeholder="введите пароль"
                :error-message="errors.password"
            />

            <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center space-x-3">
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <span>Запомнить меня</span>
                </Label>

                <TextLink :href="request()" class="mb-2 ml-auto text-sm">
                    Забыли пароль?
                </TextLink>
            </div>

            <Button
                type="submit"
                class="mt-4 w-full"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                Войти
            </Button>
        </div>

        <div
            class="text-center text-sm text-muted-foreground"
            v-if="canRegister"
        >
            Нет аккаунта?
            <TextLink :href="register()" :tabindex="5">Регистрация</TextLink>
        </div>
    </Form>
</template>
