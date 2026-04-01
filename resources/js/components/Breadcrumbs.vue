<script setup lang="ts">
import type { BreadcrumbItem as NuxtBreadcrumbItem } from '@nuxt/ui';
import type { BreadcrumbItem as BreadcrumbItemType } from '@/types';
import { computed } from 'vue';
import { toUrl } from '@/lib/utils';

interface Props {
    breadcrumbs: BreadcrumbItemType[];
}

const props = defineProps<Props>();

const items = computed<NuxtBreadcrumbItem[]>(() =>
    props.breadcrumbs.map((item, index) => ({
        label: item.title,
        to:
            index === props.breadcrumbs.length - 1
                ? undefined
                : toUrl(item.href),
    })),
);
</script>

<template>
    <UBreadcrumb
        :items="items"
        :ui="{
            list: 'flex-wrap text-sm text-muted',
            link: 'transition-colors hover:text-default',
        }"
    />
</template>
