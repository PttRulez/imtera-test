<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { toUrl, urlIsActive, visibleNavItems } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { edit as editPassword } from '@/routes/user-password';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';

const sidebarNavItems: NavItem[] = visibleNavItems([
    {
        title: 'Профиль',
        href: editProfile(),
    },
    {
        title: 'Пароль',
        href: editPassword(),
    },
    {
        title: 'Оформление',
        href: editAppearance(),
    },
]);

const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <div class="mb-8 space-y-0.5">
            <h2 class="text-xl font-semibold tracking-tight">Настройки</h2>
            <p class="text-sm text-muted-foreground">
                Управляйте вашим аккаунтом здесь
            </p>
        </div>

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1 space-x-0">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.href)"
                        variant="ghost"
                        :class="[
                            'w-full justify-start',
                            { 'bg-muted': urlIsActive(item.href, currentPath) },
                        ]"
                        as-child
                    >
                        <Link :href="item.href">
                            <component :is="item.icon" class="h-4 w-4" />
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
