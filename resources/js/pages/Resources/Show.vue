<script setup lang="ts">
import type { BreadcrumbItem } from '@/types';
import type { ResourceShowPageProps } from '@/types/resources';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';
import { useFormErrorToast } from '@/composables/useFormErrorToast';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';

const props = defineProps<ResourceShowPageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Дашборд', href: dashboard() },
    { title: 'Ресурсы', href: route('resources.index') },
    {
        title: props.resource.name,
        href: route('resources.show', props.resource.id),
    },
];

const linkForm = useForm({
    project_id: '',
    environment_id: '',
    relation_type: 'primary',
    status: 'active',
});

useFormErrorToast(
    computed(() => linkForm.errors),
    'Не удалось связать ресурс с проектом',
);

const filteredEnvironmentOptions = computed(() =>
    props.environment_options.filter(
        (environment) =>
            !linkForm.project_id ||
            environment.project_id === linkForm.project_id,
    ),
);

function linkToProject(): void {
    linkForm.transform((data) => ({
        ...data,
        resource_id: props.resource.id,
    }));

    linkForm.post(route('resources.link-project'), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="props.resource.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4">
            <section class="rounded-xl border border-default/70 bg-default p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p
                            class="text-xs tracking-[0.2em] text-muted uppercase"
                        >
                            {{ props.resource.provider }}
                        </p>
                        <h1 class="mt-2 text-2xl font-semibold tracking-tight">
                            {{ props.resource.name }}
                        </h1>
                        <p class="mt-3 text-sm text-muted">
                            {{ props.resource.kind }}. Владелец:
                            {{ props.resource.owner_user_name }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <UBadge variant="soft" color="primary">
                            {{ props.resource.sensitivity }}
                        </UBadge>
                        <UBadge variant="soft" color="neutral">
                            {{ props.resource.status }}
                        </UBadge>
                    </div>
                </div>
            </section>

            <UCard class="border border-default/70">
                <template #header>
                    <h2 class="text-lg font-semibold">
Связи с проектами
</h2>
                </template>

                <div
                    v-if="props.can_manage"
                    class="mb-4 grid gap-4 rounded-lg border border-default/60 p-4 md:grid-cols-2"
                >
                    <UFormField
                        label="Проект"
                        required
                        :error="linkForm.errors.project_id"
                    >
                        <USelectMenu
                            v-model="linkForm.project_id"
                            value-key="value"
                            class="w-full"
                            :items="props.project_options"
                        />
                    </UFormField>
                    <UFormField
                        label="Окружение"
                        :error="linkForm.errors.environment_id"
                    >
                        <USelectMenu
                            v-model="linkForm.environment_id"
                            value-key="value"
                            class="w-full"
                            :items="filteredEnvironmentOptions"
                        />
                    </UFormField>
                    <UFormField
                        label="Тип связи"
                        :error="linkForm.errors.relation_type"
                    >
                        <USelect
                            v-model="linkForm.relation_type"
                            class="w-full"
                            :items="[
                                { label: 'Основной', value: 'primary' },
                                { label: 'Общий', value: 'shared' },
                                {
                                    label: 'Поддерживающий',
                                    value: 'supporting',
                                },
                                { label: 'Только чтение', value: 'readonly' },
                            ]"
                        />
                    </UFormField>
                    <div class="flex items-end justify-end">
                        <UButton
                            :loading="linkForm.processing"
                            icon="i-lucide-link"
                            @click="linkToProject"
                        >
                            Связать ресурс
                        </UButton>
                    </div>
                </div>

                <div class="space-y-3">
                    <div
                        v-for="projectLink in props.resource.project_links"
                        :key="projectLink.id"
                        class="flex items-center justify-between rounded-lg border border-default/60 px-4 py-3"
                    >
                        <div>
                            <p class="font-medium">
                                {{ projectLink.project_name }}
                            </p>
                            <p class="text-xs text-muted">
                                {{
                                    projectLink.environment_name ||
                                    'Связь на уровне всего проекта'
                                }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <UBadge variant="soft" color="primary">
                                {{ projectLink.relation_type }}
                            </UBadge>
                            <UBadge variant="subtle" color="neutral">
                                {{ projectLink.status }}
                            </UBadge>
                        </div>
                    </div>
                </div>
            </UCard>
        </div>
    </AppLayout>
</template>
