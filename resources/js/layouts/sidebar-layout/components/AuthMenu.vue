<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupContent,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { Link } from '@inertiajs/vue3';
import { LogIn, UserRoundPlus } from 'lucide-vue-next';
import { login, register } from '@/routes';

</script>
<template>
    <SidebarGroup className="group-data-[collapsible=icon]:p-0">
        <SidebarGroupContent>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        as-child
                        class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
                    >
                        <Link :href="login()">
                            <LogIn />
                            <span>Логин</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        as-child
                        class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
                    >
                        <Link :href="register()">
                            <UserRoundPlus />
                            <span>Регистрация</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroupContent>
    </SidebarGroup>
</template>
