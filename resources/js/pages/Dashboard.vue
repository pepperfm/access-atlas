<script setup lang="ts">
import type { BreadcrumbItem } from '@/types';
import type { DashboardPageProps } from '@/types/operations';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';

const props = defineProps<DashboardPageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.title,
        href: dashboard(),
    },
];
</script>

<template>
    <Head :title="props.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="rounded-xl border border-default/70 bg-default p-6">
                <h1 class="text-2xl font-semibold tracking-tight">
                    {{ props.title }}
                </h1>
                <p class="mt-2 max-w-2xl text-sm text-muted">
                    {{ props.description }}
                </p>
            </div>
            <div class="grid gap-4 md:grid-cols-5">
                <UCard
                    v-for="metric in props.metrics"
                    :key="metric.title"
                    class="border border-default/70"
                >
                    <p class="text-xs tracking-[0.2em] text-muted uppercase">
                        {{ metric.title }}
                    </p>
                    <p class="mt-3 text-3xl font-semibold">
                        {{ metric.value }}
                    </p>
                    <p class="mt-2 text-sm text-muted">
                        {{ metric.description }}
                    </p>
                </UCard>
            </div>

            <div class="grid gap-4 xl:grid-cols-2">
                <UCard
                    v-for="section in [
                        {
                            title: 'Алерты ротации',
                            items: props.rotation_alerts,
                        },
                        {
                            title: 'Алерты доступов',
                            items: props.access_alerts,
                        },
                        {
                            title: 'Пробелы ownership',
                            items: props.ownership_alerts,
                        },
                        {
                            title: 'Ревью и инбокс',
                            items: [
                                ...props.review_alerts,
                                ...props.inbox_alerts,
                            ],
                        },
                    ]"
                    :key="section.title"
                    class="border border-default/70"
                >
                    <template #header>
                        <h2 class="text-lg font-semibold">
                            {{ section.title }}
                        </h2>
                    </template>

                    <div class="space-y-3">
                        <div
                            v-for="item in section.items"
                            :key="item.title"
                            class="rounded-lg border border-default/60 px-4 py-3"
                        >
                            <p class="font-medium">
                                {{ item.title }}
                            </p>
                            <p class="text-sm text-muted">
                                {{ item.description }}
                            </p>
                            <UButton
                                :to="item.href"
                                variant="ghost"
                                class="mt-2"
                                icon="i-lucide-arrow-right"
                            >
                                Открыть
                            </UButton>
                        </div>
                    </div>
                </UCard>
            </div>
        </div>
    </AppLayout>
</template>
