<script setup lang="ts">
import TextLink from '@/components/shared/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head } from '@inertiajs/vue3';
import FormInput from '@/components/form-elements/FormInput.vue';
import { h, VNode } from 'vue';

type H = typeof h;

defineOptions({
    layout: (h: H, page: VNode): VNode =>
        h(
            AuthLayout,
            {
                title: 'Регистрация',
                description: 'Заполните форму регистрации',
            },
            () => page,
        ),
});
</script>

<template>
    <Head title="Register" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <FormInput
                field-name="name"
                placeholder="ваш имя"
                :error-message="errors.name"
            />

            <FormInput
                field-name="email"
                type="email"
                placeholder="ваш email"
                :error-message="errors.email"
            />

            <FormInput
                field-name="password"
                type="password"
                placeholder="придумайте пароль"
                :error-message="errors.password"
            />

            <FormInput
                field-name="password_confirmation"
                type="password"
                placeholder="повторите пароль"
                :error-message="errors.password_confirmation"
            />

            <Button
                type="submit"
                class="mt-2 w-full"
                tabindex="5"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                Создать аккаунт
            </Button>
        </div>

        <div class="text-center text-sm text-muted-foreground">
            Уже есть аккаунт?
            <TextLink
                :href="login()"
                class="underline underline-offset-4"
                :tabindex="6"
                >Войти</TextLink
            >
        </div>
    </Form>
</template>
