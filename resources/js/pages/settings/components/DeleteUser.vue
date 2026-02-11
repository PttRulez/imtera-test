<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { Form } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';

// Components
import FormInput from '@/components/form-elements/FormInput.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import HeadingSmall from '@/pages/settings/components/HeadingSmall.vue';

const passwordInput = useTemplateRef('passwordInput');
</script>

<template>
    <div class="space-y-6">
        <HeadingSmall
            title="Удаление аккаунта"
            description="Удаление аккаунта и всего, что к нему относится"
        />
        <div
            class="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10"
        >
            <div class="relative space-y-0.5 text-red-600 dark:text-red-100">
                <p class="text-sm">
                    Пожалуйста, будьте осторожны. Это действие нельзя отменить.
                </p>
            </div>
            <Dialog>
                <DialogTrigger as-child>
                    <Button variant="destructive" data-test="delete-user-button"
                        >Удалить аккаунт</Button
                    >
                </DialogTrigger>
                <DialogContent>
                    <Form
                        v-bind="ProfileController.destroy.form()"
                        reset-on-success
                        @error="() => passwordInput?.$el?.focus()"
                        :options="{
                            preserveScroll: true,
                        }"
                        class="space-y-6"
                        v-slot="{ errors, processing, reset, clearErrors }"
                    >
                        <DialogHeader class="space-y-3">
                            <DialogTitle
                                >Вы уверены, что хотите удалить
                                аккаунт?</DialogTitle
                            >
                            <DialogDescription>
                                Как только вы удалите аккаунт, все ресурсы и
                                данные также будут удалены. Пожалуйста, введите
                                ваш пароль, чтобы подтвердить свои намерения
                                навсегда удалить аккаунт.
                            </DialogDescription>
                        </DialogHeader>

                        <FormInput
                            field-name="password"
                            type="password"
                            placeholder="пароль"
                            :error-message="errors.password"
                        />

                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button
                                    variant="secondary"
                                    @click="
                                        () => {
                                            clearErrors();
                                            reset();
                                        }
                                    "
                                >
                                    Отмена
                                </Button>
                            </DialogClose>

                            <Button
                                type="submit"
                                variant="destructive"
                                :disabled="processing"
                                data-test="confirm-delete-user-button"
                            >
                                Удалить аккаунт
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template>
