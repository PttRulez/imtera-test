<script setup lang="ts">
import Breadcrumbs from '@/components/shared/Breadcrumbs.vue';
import {
    SidebarInset,
    SidebarProvider,
    SidebarTrigger,
} from '@/components/ui/sidebar';
import Sidebar from '@/layouts/sidebar-layout/Sidebar.vue';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}
withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const isOpen = usePage().props.sidebarOpen;
</script>

<template>
    <SidebarProvider :default-open="isOpen">
        <Sidebar />
        <SidebarInset class="overflow-x-hidden">
            <header
                class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
            >
                <div class="flex items-center gap-2">
                    <SidebarTrigger class="-ml-1" />
                    <Breadcrumbs
                        v-if="breadcrumbs && breadcrumbs.length > 0"
                        :breadcrumbs="breadcrumbs"
                    />
                </div>
            </header>
            <main class="p-5 h-full">
                <slot />
            </main>
        </SidebarInset>
    </SidebarProvider>
</template>
