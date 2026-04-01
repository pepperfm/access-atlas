<script setup lang="ts">
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Heading from '@/components/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/appearance';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Настройки внешнего вида',
        href: edit(),
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Настройки внешнего вида" />

        <h1 class="sr-only">
Настройки внешнего вида
</h1>

        <SettingsLayout>
            <div class="space-y-6">
                <Heading
                    variant="small"
                    title="Настройки внешнего вида"
                    description="Настройте внешний вид аккаунта"
                />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
