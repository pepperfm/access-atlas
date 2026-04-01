<script setup lang="ts">
import type { BreadcrumbItem, ProjectShowPageProps } from '@/types';
import type { RevealedSecret, Secret } from '@/types/secrets';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import { useFormErrorToast } from '@/composables/useFormErrorToast';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    criticalityLabel,
    projectRoleLabel,
    projectStatusLabel,
    secretStorageModeLabel,
    sensitivityLabel,
} from '@/lib/labels';
import { dashboard } from '@/routes';

const props = defineProps<ProjectShowPageProps>();
const ENV_KEY_REPLACE_PATTERN = /[^a-z0-9]+/gi;
const ENV_KEY_TRIM_PATTERN = /^_+|_+$/g;

const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Дашборд', href: dashboard() },
    { title: 'Проекты', href: route('projects.index') },
    {
        title: props.project.name,
        href: route('projects.show', props.project.id),
    },
];

const secretSearch = ref('');
const revealReasonOpen = ref(false);
const revealedSecretOpen = ref(false);
const secretPendingReveal = ref<Secret | null>(null);

const projectForm = useForm({
    name: props.project.name,
    repository_url: props.project.repository_url || '',
    description: props.project.description || '',
    criticality: props.project.criticality,
});

const membershipForm = useForm({
    user_ids: [] as string[],
    project_role: 'developer',
});

const revealForm = useForm({
    reason: '',
});

useFormErrorToast(
    computed(() => projectForm.errors),
    'Не удалось обновить проект',
);
useFormErrorToast(
    computed(() => membershipForm.errors),
    'Не удалось добавить пользователя в проект',
);
useFormErrorToast(
    computed(() => revealForm.errors),
    'Не удалось раскрыть секрет',
);

const userOptions = computed(() =>
    props.user_options.map((user) => ({
        label: `${user.name} (${user.email})`,
        value: user.id,
    })),
);

const filteredSecrets = computed(() => {
    const query = secretSearch.value.toLowerCase().trim();

    if (!query) {
        return props.secrets;
    }

    return props.secrets.filter(
        (secret) =>
            secret.name.toLowerCase().includes(query) ||
            secret.environment_name.toLowerCase().includes(query) ||
            secret.secret_type.toLowerCase().includes(query),
    );
});

const revealedSecret = computed<RevealedSecret | null>(
    () =>
        (page.props.flash?.revealed_secret as
            | RevealedSecret
            | null
            | undefined) ?? null,
);

watch(
    revealedSecret,
    (value) => {
        if (!value) {
            return;
        }

        revealedSecretOpen.value = true;
    },
    { immediate: true },
);

const envKey = computed(() => {
    const source = revealedSecret.value?.name ?? '';

    return source
        .trim()
        .replaceAll(ENV_KEY_REPLACE_PATTERN, '_')
        .replaceAll(ENV_KEY_TRIM_PATTERN, '')
        .toUpperCase();
});

const envLine = computed(() =>
    revealedSecret.value?.value
        ? `${envKey.value}=${revealedSecret.value.value}`
        : '',
);

function updateProject(): void {
    projectForm.put(route('projects.update', props.project.id), {
        preserveScroll: true,
        errorBag: 'updateProject',
    });
}

function addParticipants(): void {
    if (membershipForm.user_ids.length === 0) {
        return;
    }

    let completed = 0;
    const total = membershipForm.user_ids.length;
    const selectedIds = [...membershipForm.user_ids];

    membershipForm.processing = true;

    selectedIds.forEach((userId, index) => {
        router.post(
            route('projects.users.store', props.project.id),
            {
                user_id: userId,
                project_role: membershipForm.project_role,
            },
            {
                preserveScroll: true,
                preserveState: index < total - 1,
                onFinish: () => {
                    completed++;

                    if (completed === total) {
                        membershipForm.processing = false;
                        membershipForm.reset('user_ids', 'project_role');
                        membershipForm.project_role = 'developer';
                    }
                },
            },
        );
    });
}

function removeParticipant(userId: string): void {
    router.delete(
        route('projects.users.destroy', {
            project: props.project.id,
            user: userId,
        }),
        {
            preserveScroll: true,
        },
    );
}

function archiveProject(): void {
    router.post(route('projects.archive', props.project.id));
}

function startReveal(secret: Secret): void {
    secretPendingReveal.value = secret;
    revealForm.clearErrors();
    revealForm.reason = '';

    if (['high', 'critical'].includes(secret.sensitivity)) {
        revealReasonOpen.value = true;
        return;
    }

    submitReveal(secret.id);
}

function submitReveal(secretId: string): void {
    revealForm
        .transform((data) => ({
            reason: data.reason || null,
        }))
        .post(route('secrets.reveal', secretId), {
            preserveScroll: true,
            errorBag: 'revealSecret',
            onSuccess: () => {
                revealReasonOpen.value = false;
                revealForm.reset();
                secretPendingReveal.value = null;
            },
        });
}

async function copyText(value: string, successTitle: string): Promise<void> {
    const toast = useToast();

    try {
        await navigator.clipboard.writeText(value);

        toast.add({
            color: 'success',
            title: successTitle,
        });
    } catch {
        toast.add({
            color: 'error',
            title: 'Не удалось скопировать значение',
        });
    }
}

async function copySecretValue(): Promise<void> {
    if (!revealedSecret.value?.value) {
        return;
    }

    await copyText(revealedSecret.value.value, 'Значение скопировано');
}

async function copyEnvLine(): Promise<void> {
    if (!envLine.value) {
        return;
    }

    await copyText(envLine.value, 'ENV-строка скопирована');
}

async function copyReference(): Promise<void> {
    if (!revealedSecret.value?.external_reference) {
        return;
    }

    await copyText(
        revealedSecret.value.external_reference,
        'Reference скопирован',
    );
}
</script>

<template>
    <Head :title="props.project.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4">
            <UCard>
                <template #header>
                    <div
                        class="flex flex-wrap items-start justify-between gap-4"
                    >
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <h1
                                    class="text-2xl font-semibold tracking-tight"
                                >
                                    {{ props.project.name }}
                                </h1>
                                <UBadge variant="soft" color="neutral">
                                    {{
                                        criticalityLabel(
                                            props.project.criticality,
                                        )
                                    }}
                                </UBadge>
                                <UBadge variant="soft" color="primary">
                                    {{
                                        projectStatusLabel(props.project.status)
                                    }}
                                </UBadge>
                            </div>

                            <p class="text-sm text-muted">
                                {{
                                    props.project.description ||
                                    'Краткое описание проекта пока не заполнено.'
                                }}
                            </p>

                            <p
                                class="text-xs tracking-[0.24em] text-muted uppercase"
                            >
                                {{ props.project.key }}
                            </p>
                        </div>

                        <UButton
                            v-if="
                                props.can_archive && !props.project.archived_at
                            "
                            color="error"
                            variant="soft"
                            icon="i-lucide-archive"
                            @click="archiveProject"
                        >
                            Архивировать
                        </UButton>
                    </div>
                </template>

                <p v-if="props.project.repository_url" class="text-sm">
                    <ULink
                        :to="props.project.repository_url"
                        target="_blank"
                        class="text-primary"
                    >
                        Открыть репозиторий
                    </ULink>
                </p>
            </UCard>

            <section class="grid gap-4 xl:grid-cols-[1.2fr_0.8fr]">
                <UCard>
                    <template #header>
                        <div class="space-y-1">
                            <h2 class="text-lg font-semibold">
                                Настройки проекта
                            </h2>
                            <p class="text-sm text-muted">
                                Название, репозиторий и критичность можно менять
                                здесь.
                            </p>
                        </div>
                    </template>

                    <UForm
                        :state="projectForm"
                        class="grid gap-4 md:grid-cols-2"
                        @submit.prevent="updateProject"
                    >
                        <UFormField
                            label="Название проекта"
                            name="name"
                            required
                            :error="projectForm.errors.name"
                        >
                            <UInput v-model="projectForm.name" class="w-full" />
                        </UFormField>

                        <UFormField
                            label="Ссылка на репозиторий"
                            name="repository_url"
                            :error="projectForm.errors.repository_url"
                        >
                            <UInput
                                v-model="projectForm.repository_url"
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField
                            label="Критичность"
                            name="criticality"
                            :error="projectForm.errors.criticality"
                        >
                            <USelect
                                v-model="projectForm.criticality"
                                class="w-full"
                                :items="[
                                    { label: 'Низкая', value: 'low' },
                                    { label: 'Средняя', value: 'medium' },
                                    { label: 'Высокая', value: 'high' },
                                    { label: 'Критическая', value: 'critical' },
                                ]"
                            />
                        </UFormField>

                        <UFormField
                            label="Комментарий"
                            name="description"
                            class="md:col-span-2"
                            :error="projectForm.errors.description"
                        >
                            <UTextarea
                                v-model="projectForm.description"
                                class="w-full"
                                :rows="4"
                                placeholder="Что это за проект и зачем он нужен."
                            />
                        </UFormField>

                        <div class="flex justify-end md:col-span-2">
                            <UButton
                                v-if="props.can_manage"
                                :loading="projectForm.processing"
                                icon="i-lucide-save"
                                type="submit"
                            >
                                Сохранить проект
                            </UButton>
                        </div>
                    </UForm>
                </UCard>

                <div class="space-y-4">
                    <UCard>
                        <template #header>
                            <h2 class="text-lg font-semibold">
                                Добавить участников
                            </h2>
                        </template>

                        <UForm
                            :state="membershipForm"
                            class="space-y-4"
                            @submit.prevent="addParticipants"
                        >
                            <UFormField
                                label="Пользователи"
                                name="user_ids"
                                required
                                :error="membershipForm.errors.user_ids"
                            >
                                <USelectMenu
                                    v-model="membershipForm.user_ids"
                                    :items="userOptions"
                                    value-key="value"
                                    multiple
                                    class="w-full"
                                    placeholder="Выберите одного или нескольких"
                                />
                            </UFormField>

                            <UFormField
                                label="Роль в проекте"
                                name="project_role"
                                help="Выбери роль людей внутри этого проекта. App-role пользователей при этом не меняется."
                                :error="membershipForm.errors.project_role"
                            >
                                <URadioGroup
                                    v-model="membershipForm.project_role"
                                    variant="card"
                                    indicator="end"
                                    orientation="horizontal"
                                    :ui="{ item: 'flex-1' }"
                                    :items="[
                                        { label: 'Владелец', value: 'owner' },
                                        { label: 'Техлид', value: 'tech_lead' },
                                        { label: 'Менеджер', value: 'manager' },
                                        {
                                            label: 'Разработчик',
                                            value: 'developer',
                                        },
                                    ]"
                                />
                            </UFormField>

                            <div class="flex justify-end">
                                <UButton
                                    :loading="membershipForm.processing"
                                    :disabled="
                                        membershipForm.user_ids.length === 0
                                    "
                                    icon="i-lucide-user-plus"
                                    type="submit"
                                >
                                    {{
                                        membershipForm.user_ids.length > 1
                                            ? `Прикрепить ${membershipForm.user_ids.length} чел.`
                                            : 'Прикрепить к проекту'
                                    }}
                                </UButton>
                            </div>
                        </UForm>
                    </UCard>

                    <UCard>
                        <template #header>
                            <h2 class="text-lg font-semibold">
Окружения
</h2>
                        </template>

                        <div class="space-y-3">
                            <div
                                v-for="environment in props.project
                                    .environments"
                                :key="environment.id"
                                class="flex items-center justify-between gap-3"
                            >
                                <div>
                                    <p class="font-medium">
                                        {{ environment.name }}
                                    </p>
                                    <p
                                        class="text-xs tracking-[0.2em] text-muted uppercase"
                                    >
                                        {{ environment.key }}
                                    </p>
                                </div>

                                <UBadge
                                    v-if="environment.is_production"
                                    color="warning"
                                    variant="soft"
                                >
                                    prod
                                </UBadge>
                            </div>
                        </div>
                    </UCard>
                </div>
            </section>

            <UCard>
                <template #header>
                    <div
                        class="flex flex-wrap items-center justify-between gap-4"
                    >
                        <h2 class="text-lg font-semibold">
Команда проекта
</h2>
                    </div>
                </template>

                <div class="space-y-3">
                    <div
                        v-for="membership in props.project.memberships"
                        :key="membership.id"
                        class="flex flex-wrap items-center justify-between gap-3"
                    >
                        <div>
                            <p class="font-medium">
                                {{ membership.user_name }}
                            </p>
                            <p class="text-sm text-muted">
                                {{ projectRoleLabel(membership.project_role) }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <UBadge color="neutral" variant="soft">
                                {{ projectStatusLabel(membership.status) }}
                            </UBadge>

                            <UButton
                                v-if="
                                    props.can_manage &&
                                    membership.status !== 'left'
                                "
                                color="error"
                                variant="ghost"
                                size="xs"
                                icon="i-lucide-user-minus"
                                @click="removeParticipant(membership.user_id)"
                            />
                        </div>
                    </div>
                </div>
            </UCard>

            <UCard v-if="props.secrets.length > 0">
                <template #header>
                    <div
                        class="flex flex-wrap items-center justify-between gap-4"
                    >
                        <div class="space-y-1">
                            <h2 class="text-lg font-semibold">
                                Секреты проекта
                            </h2>
                            <p class="text-sm text-muted">
                                Все секреты, привязанные к этому проекту.
                                Раскройте нужный и скопируйте.
                            </p>
                        </div>

                        <UInput
                            v-if="props.secrets.length > 3"
                            v-model="secretSearch"
                            icon="i-lucide-search"
                            placeholder="Поиск секретов"
                            class="w-full max-w-xs"
                        />
                    </div>
                </template>

                <div class="grid gap-4 xl:grid-cols-3">
                    <UCard
                        v-for="secret in filteredSecrets"
                        :key="secret.id"
                        variant="outline"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <p class="font-medium">
                                    {{ secret.name }}
                                </p>
                                <p class="text-sm text-muted">
                                    {{ secret.environment_name }} ·
                                    {{
                                        secret.secret_type.replaceAll('_', ' ')
                                    }}
                                </p>
                            </div>

                            <UBadge variant="soft" color="neutral" size="xs">
                                {{ sensitivityLabel(secret.sensitivity) }}
                            </UBadge>
                        </div>

                        <div
                            class="mt-3 flex items-center justify-between gap-3"
                        >
                            <p class="text-xs text-muted">
                                {{
                                    secret.storage_mode === 'encrypted_value'
                                        ? 'Зашифровано'
                                        : secretStorageModeLabel(
                                              secret.storage_mode,
                                          )
                                }}
                            </p>

                            <UButton
                                v-if="props.can_reveal_secrets"
                                variant="soft"
                                color="warning"
                                size="xs"
                                icon="i-lucide-eye"
                                @click="startReveal(secret)"
                            >
                                Раскрыть
                            </UButton>
                        </div>
                    </UCard>
                </div>

                <p
                    v-if="filteredSecrets.length === 0 && secretSearch"
                    class="py-4 text-center text-sm text-muted"
                >
                    Ничего не найдено по запросу «{{ secretSearch }}»
                </p>
            </UCard>

            <UModal
                v-model:open="revealReasonOpen"
                title="Причина раскрытия"
                :description="
                    secretPendingReveal
                        ? `Секрет «${secretPendingReveal.name}» имеет повышенную чувствительность. Укажи причину раскрытия.`
                        : 'Укажи причину раскрытия секрета.'
                "
            >
                <template #body>
                    <UForm
                        :state="revealForm"
                        class="space-y-4"
                        @submit.prevent="
                            secretPendingReveal &&
                            submitReveal(secretPendingReveal.id)
                        "
                    >
                        <UFormField
                            label="Причина"
                            name="reason"
                            required
                            help="Причина попадёт в аудит, чтобы было понятно, зачем секрет раскрывали."
                            :error="revealForm.errors.reason"
                        >
                            <UTextarea
                                v-model="revealForm.reason"
                                class="w-full"
                                :rows="4"
                                placeholder="Например: нужно добавить ключ в production env для ротации"
                            />
                        </UFormField>
                    </UForm>
                </template>

                <template #footer>
                    <div class="flex justify-end gap-3">
                        <UButton
                            color="neutral"
                            variant="subtle"
                            @click="revealReasonOpen = false"
                        >
                            Отмена
                        </UButton>
                        <UButton
                            color="warning"
                            icon="i-lucide-eye"
                            :loading="revealForm.processing"
                            @click="
                                secretPendingReveal &&
                                submitReveal(secretPendingReveal.id)
                            "
                        >
                            Раскрыть секрет
                        </UButton>
                    </div>
                </template>
            </UModal>

            <UModal
                v-model:open="revealedSecretOpen"
                :title="
                    revealedSecret
                        ? `Секрет: ${revealedSecret.name}`
                        : 'Раскрытый секрет'
                "
                :description="
                    revealedSecret?.is_reference_only
                        ? 'В этом случае показывается только внешний reference.'
                        : 'Значение раскрыто один раз и уже зааудировано.'
                "
            >
                <template #body>
                    <div v-if="revealedSecret" class="space-y-4">
                        <UAlert
                            color="warning"
                            variant="soft"
                            icon="i-lucide-shield-alert"
                            title="Действуй аккуратно"
                            description="Не пересылай секрет в чаты и не оставляй его в заметках. Лучше сразу скопировать и использовать по назначению."
                        />

                        <UCard variant="subtle">
                            <template #header>
                                <div
                                    class="flex items-center justify-between gap-3"
                                >
                                    <div>
                                        <p
                                            class="text-xs font-medium tracking-[0.2em] text-muted uppercase"
                                        >
                                            {{
                                                revealedSecret.is_reference_only
                                                    ? 'Reference'
                                                    : 'Значение'
                                            }}
                                        </p>
                                        <p class="mt-1 text-sm font-medium">
                                            {{ revealedSecret.name }}
                                        </p>
                                    </div>
                                    <UBadge variant="soft" color="neutral">
                                        {{
                                            revealedSecret.is_reference_only
                                                ? 'Внешний источник'
                                                : 'Encrypted value'
                                        }}
                                    </UBadge>
                                </div>
                            </template>

                            <UTextarea
                                :model-value="
                                    revealedSecret.is_reference_only
                                        ? revealedSecret.external_reference ||
                                          ''
                                        : revealedSecret.value || ''
                                "
                                :rows="4"
                                readonly
                                class="w-full"
                            />
                        </UCard>

                        <UCard
                            v-if="!revealedSecret.is_reference_only && envLine"
                            variant="outline"
                        >
                            <template #header>
                                <div class="space-y-1">
                                    <h3 class="text-base font-semibold">
                                        Готово для `.env`
                                    </h3>
                                    <p class="text-sm text-muted">
                                        Можно сразу вставить в переменные
                                        окружения.
                                    </p>
                                </div>
                            </template>

                            <UTextarea
                                :model-value="envLine"
                                :rows="3"
                                readonly
                                class="w-full"
                            />
                        </UCard>

                        <UAlert
                            v-if="revealedSecret.reason"
                            color="neutral"
                            variant="soft"
                            icon="i-lucide-file-text"
                            title="Причина раскрытия"
                            :description="revealedSecret.reason"
                        />
                    </div>
                </template>

                <template #footer>
                    <div class="flex flex-wrap justify-end gap-3">
                        <UButton
                            v-if="revealedSecret?.is_reference_only"
                            icon="i-lucide-copy"
                            @click="copyReference"
                        >
                            Скопировать reference
                        </UButton>

                        <template v-else>
                            <UButton
                                icon="i-lucide-copy"
                                @click="copySecretValue"
                            >
                                Скопировать значение
                            </UButton>
                            <UButton
                                color="neutral"
                                variant="soft"
                                icon="i-lucide-braces"
                                @click="copyEnvLine"
                            >
                                Скопировать как ENV
                            </UButton>
                        </template>
                    </div>
                </template>
            </UModal>
        </div>
    </AppLayout>
</template>
