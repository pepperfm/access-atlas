<script setup lang="ts">
import type { BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
