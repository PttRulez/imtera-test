<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppearanceTabs from '@/pages/settings/components/AppearanceTabs.vue';
import HeadingSmall from '@/pages/settings/components/HeadingSmall.vue';
import SettingsSettingsWrapper from '@/pages/settings/components/SettingsWrapper.vue';

</script>

<template>
    <Head title="Настройки оформления" />

    <SettingsSettingsWrapper>
        <div class="space-y-6">
            <HeadingSmall
                title="Оформления"
                description="Настройки внешнего вида сайта"
            />
            <AppearanceTabs />
        </div>
    </SettingsSettingsWrapper>
</template>
