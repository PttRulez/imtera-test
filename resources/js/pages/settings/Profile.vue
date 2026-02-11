<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { send } from '@/routes/verification';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import FormInput from '@/components/form-elements/FormInput.vue';
import { Button } from '@/components/ui/button';
import SettingsWrapper from '@/pages/settings/components/SettingsWrapper.vue';
import DeleteUser from '@/pages/settings/components/DeleteUser.vue';
import HeadingSmall from '@/pages/settings/components/HeadingSmall.vue';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const page = usePage();
const user = page.props.auth.user;
</script>

<template>
    <Head title="Настройки профиля" />

    <SettingsWrapper>
        <div class="flex flex-col space-y-6">
            <HeadingSmall
                title="Информация о профиле"
                description="Здесь можно править информацию о вас"
            />

            <Form
                v-bind="ProfileController.update.form()"
                class="space-y-6"
                v-slot="{ errors, processing, recentlySuccessful }"
            >
                <FormInput
                    field-name="name"
                    placeholder="ваш имя"
                    :error-message="errors.name"
                    v-model="user.name"
                />

                <FormInput
                    field-name="email"
                    type="email"
                    placeholder="ваш email"
                    :error-message="errors.email"
                    v-model="user.email"
                />

                <div v-if="mustVerifyEmail && !user.email_verified_at">
                    <p class="-mt-4 text-sm text-muted-foreground">
                        Ваш email не подтвержден.
                        <Link
                            :href="send()"
                            as="button"
                            class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                        >
                            Нажмите, чтобы получить email для подтверждения.
                        </Link>
                    </p>

                    <div
                        v-if="status === 'verification-link-sent'"
                        class="mt-2 text-sm font-medium text-green-600"
                    >
                        Новая ссылка для подтверждения email отправлена вам на
                        почту.
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Button
                        :disabled="processing"
                        data-test="update-profile-button"
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

        <DeleteUser />
    </SettingsWrapper>
</template>
