<script setup lang="ts">
import SidebarLayout from '@/layouts/sidebar-layout/SidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
<!--    <SidebarLayout :breadcrumbs="breadcrumbs">-->
    <main class="p-5">
        <slot />
    </main>
<!--    </SidebarLayout>-->
</template>
