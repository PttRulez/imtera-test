<script setup lang="ts">
import Header from '@/layouts/header-layout/Header.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <div class="flex min-h-screen w-full flex-col">
        <Header :breadcrumbs="breadcrumbs" />
        <main
            class="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl"
        >
            <slot />
        </main>
    </div>
</template>
