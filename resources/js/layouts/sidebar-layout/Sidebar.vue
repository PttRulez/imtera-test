<script setup lang="ts">
import Main from '@/layouts/sidebar-layout/components/Main.vue';
import UserMenuTrigger from '@/layouts/sidebar-layout/components/UserMenuTrigger.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { visibleNavItems } from '@/lib/utils';
import { home } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid } from 'lucide-vue-next';
import AppLogo from '../components/AppLogo.vue';
import { computed } from 'vue';
import AuthMenu from '@/layouts/sidebar-layout/components/AuthMenu.vue';

const page = usePage();
const auth = computed(() => page.props.auth);

const mainNavItems: NavItem[] = visibleNavItems([
    {
        title: 'Главная',
        href: home(),
        icon: LayoutGrid,
    },
]);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="home()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <Main :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <UserMenuTrigger v-if="auth.user" :user="auth.user"/>
            <AuthMenu v-else />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
