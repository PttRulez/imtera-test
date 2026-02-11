<script setup lang="ts">
import PasswordController from '@/actions/App/Http/Controllers/Settings/PasswordController';
import SettingsSettingsWrapper from '@/pages/settings/components/SettingsWrapper.vue';
import { Form, Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import HeadingSmall from '@/pages/settings/components/HeadingSmall.vue';
import FormInput from '@/components/form-elements/FormInput.vue';

</script>

<template>
    <Head title="Смена пароля" />

    <SettingsSettingsWrapper>
        <div class="space-y-6">
            <HeadingSmall
                title="Смена пароля"
                description="Старайтесь использовать длинный случайный пароль для своей безопасности"
            />

            <Form
                v-bind="PasswordController.update.form()"
                :options="{
                    preserveScroll: true,
                }"
                reset-on-success
                :reset-on-error="[
                    'password',
                    'password_confirmation',
                    'current_password',
                ]"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <FormInput
                    field-name="current_password"
                    type="password"
                    label="Текущий пароль"
                    :error-message="errors.current_password"
                />

                <FormInput
                    field-name="password"
                    type="password"
                    label="Новый пароль"
                    :error-message="errors.password"
                />

                <FormInput
                    field-name="password_confirmation"
                    type="password"
                    label="Повтор нового пароля"
                    :error-message="errors.password_confirmation"
                />

                <div class="flex items-center gap-4">
                    <Button
                        :disabled="processing"
                        data-test="update-password-button"
                        >Сохранить</Button
                    >

                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p
                            v-show="recentlySuccessful"
                            class="text-sm text-neutral-600"
                        >
                            Сохранено
                        </p>
                    </Transition>
                </div>
            </Form>
        </div>
    </SettingsSettingsWrapper>
</template>
