<script setup lang="ts">
import type { BreadcrumbItem } from '@/types';
import type {
    RevealedSecret,
    Secret,
    SecretsIndexPageProps,
} from '@/types/secrets';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import { useFormErrorToast } from '@/composables/useFormErrorToast';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    projectStatusLabel,
    secretStorageModeLabel,
    sensitivityLabel,
} from '@/lib/labels';
import { dashboard } from '@/routes';

const props = defineProps<SecretsIndexPageProps>();
const ENV_KEY_REPLACE_PATTERN = /[^a-z0-9]+/gi;
const ENV_KEY_TRIM_PATTERN = /^_+|_+$/g;

const page = usePage();
const showAdvanced = ref(false);
const revealReasonOpen = ref(false);
const revealedSecretOpen = ref(false);
const secretPendingReveal = ref<Secret | null>(null);
const secretSearch = ref('');

const filteredSecrets = computed(() => {
    const query = secretSearch.value.toLowerCase().trim();

    if (!query) {
        return props.secrets;
    }

    return props.secrets.filter(
        (secret) =>
            secret.name.toLowerCase().includes(query) ||
            secret.project_name.toLowerCase().includes(query) ||
            secret.environment_name.toLowerCase().includes(query) ||
            secret.secret_type.toLowerCase().includes(query),
    );
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Дашборд', href: dashboard() },
    { title: 'Секреты', href: route('secrets.index') },
];

const storageModeOptions = [
    {
        value: 'encrypted_value',
        title: 'Вставить сырое значение',
    },
    {
        value: 'external_reference',
        title: 'Сохранить ссылку или путь',
    },
] as const;

const secretTypeOptions = ref([
    { label: 'API key', value: 'api_key' },
    { label: 'Пароль', value: 'password' },
    { label: 'Token', value: 'token' },
    { label: 'Webhook secret', value: 'webhook_secret' },
    { label: 'SSH key', value: 'ssh_key' },
    { label: 'Client secret', value: 'client_secret' },
    { label: 'Connection string', value: 'connection_string' },
    { label: 'Другое', value: 'other' },
]);

const sensitivityOptions = [
    { label: 'Низкая', value: 'low' },
    { label: 'Средняя', value: 'medium' },
    { label: 'Высокая', value: 'high' },
    { label: 'Критическая', value: 'critical' },
];

const revealPolicyOptions = [
    {
        label: 'Владелец проекта или security admin',
        value: 'project_owner_or_security_admin',
    },
    {
        label: 'Владелец, техлид или менеджер проекта',
        value: 'project_manager_or_higher',
    },
    { label: 'Только security admin', value: 'security_admin_only' },
    { label: 'Индивидуальное правило', value: 'custom_manual_rule' },
];

const form = useForm({
    project_id: '',
    environment_id: '',
    resource_id: '',
    name: '',
    secret_type: 'api_key',
    sensitivity: 'medium',
    storage_mode: 'encrypted_value' as 'encrypted_value' | 'external_reference',
    external_reference: '',
    encrypted_value: '',
    owner_user_id: props.default_owner_user_id || '',
    status: 'active',
    reveal_policy: 'project_owner_or_security_admin',
    rotation_due_at: '',
    notes: '',
});

useFormErrorToast(
    computed(() => form.errors),
    'Не удалось сохранить секрет',
);

const revealForm = useForm({
    reason: '',
});

useFormErrorToast(
    computed(() => revealForm.errors),
    'Не удалось раскрыть секрет',
);

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

const isEncryptedMode = computed(() => form.storage_mode === 'encrypted_value');
const storageSummary = computed(() =>
    isEncryptedMode.value
        ? 'Сырое значение будет зашифровано на сервере и не попадет в обычные списки.'
        : 'В реестре сохранится только ссылка или путь на внешний источник истины.',
);
const currentStep = computed(() => {
    if (!form.project_id || !form.environment_id || !form.name) {
        return 1;
    }

    if (
        isEncryptedMode.value ? !form.encrypted_value : !form.external_reference
    ) {
        return 2;
    }

    return 3;
});
const stepperItems = [
    {
        value: 1,
        title: 'Контекст',
        description: 'Проект, окружение и название.',
    },
    {
        value: 2,
        title: 'Хранение',
        description: 'Сырое значение или внешний reference.',
    },
    {
        value: 3,
        title: 'Контроль',
        description: 'Ответственный и доп. политика.',
    },
];

function setStorageMode(mode: 'encrypted_value' | 'external_reference'): void {
    form.storage_mode = mode;

    if (mode === 'encrypted_value') {
        form.external_reference = '';
        return;
    }

    form.encrypted_value = '';
}

function handleStorageModeChange(value: string | number | undefined): void {
    if (value === 'encrypted_value' || value === 'external_reference') {
        setStorageMode(value);
    }
}

function createSecretType(value: string): void {
    const normalized = value.trim();

    if (!normalized) {
        return;
    }

    const optionValue = normalized.toLowerCase().replaceAll(' ', '_');

    if (
        secretTypeOptions.value.some((option) => option.value === optionValue)
    ) {
        form.secret_type = optionValue;
        return;
    }

    secretTypeOptions.value.push({
        label: normalized,
        value: optionValue,
    });

    form.secret_type = optionValue;
}

const filteredEnvironmentOptions = computed(() =>
    props.environment_options.filter(
        (environment) =>
            !form.project_id || environment.project_id === form.project_id,
    ),
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

function createSecret(): void {
    form.transform((data) => ({
        ...data,
        resource_id: data.resource_id || null,
        owner_user_id: data.owner_user_id || null,
        external_reference: isEncryptedMode.value
            ? null
            : data.external_reference || null,
        encrypted_value: isEncryptedMode.value
            ? data.encrypted_value || null
            : null,
        rotation_due_at: data.rotation_due_at || null,
        notes: data.notes || null,
    })).post(route('secrets.store'), {
        preserveScroll: true,
        errorBag: 'createSecret',
        onSuccess: () => {
            const preservedContext = {
                project_id: form.project_id,
                environment_id: form.environment_id,
                owner_user_id: form.owner_user_id,
                storage_mode: form.storage_mode,
                sensitivity: form.sensitivity,
                reveal_policy: form.reveal_policy,
            };

            form.defaults({
                ...preservedContext,
                resource_id: '',
                name: '',
                secret_type: 'api_key',
                external_reference: '',
                encrypted_value: '',
                status: 'active',
                rotation_due_at: '',
                notes: '',
            });

            form.reset();
            form.project_id = preservedContext.project_id;
            form.environment_id = preservedContext.environment_id;
            form.owner_user_id = preservedContext.owner_user_id;
            form.storage_mode = preservedContext.storage_mode;
            form.sensitivity = preservedContext.sensitivity;
            form.reveal_policy = preservedContext.reveal_policy;
            form.secret_type = 'api_key';
        },
    });
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
    <Head title="Секреты" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4">
            <section class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
                <UCard variant="subtle">
                    <template #header>
                        <div class="space-y-3">
                            <div>
                                <p
                                    class="text-xs font-medium tracking-[0.24em] text-muted uppercase"
                                >
                                    Secret Intake
                                </p>
                                <h1
                                    class="mt-2 text-2xl font-semibold tracking-tight"
                                >
                                    Добавление секретов
                                </h1>
                            </div>

                            <p class="text-sm text-muted">
                                Сначала определяем контекст, потом способ
                                хранения. Если вставляете пароль, token или key
                                прямо сюда, значение будет зашифровано при
                                сохранении.
                            </p>
                        </div>
                    </template>

                    <UStepper
                        :items="stepperItems"
                        :model-value="currentStep"
                        disabled
                    />
                </UCard>

                <UCard variant="soft">
                    <template #header>
                        <div class="space-y-2">
                            <p
                                class="text-xs font-medium tracking-[0.24em] text-muted uppercase"
                            >
                                Как это увидит команда
                            </p>
                            <h2 class="text-xl font-semibold tracking-tight">
                                {{ form.name || 'Новый секрет' }}
                            </h2>
                            <p class="text-sm text-muted">
                                {{
                                    form.project_id
                                        ? 'Контекст уже выбран, осталось только корректно зафиксировать сам секрет.'
                                        : 'Сначала выберите проект и окружение, чтобы реестр понял контекст.'
                                }}
                            </p>
                        </div>
                    </template>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <UCard variant="outline">
                                <p
                                    class="text-xs font-medium tracking-[0.2em] text-muted uppercase"
                                >
                                    Режим
                                </p>
                                <p class="mt-2 text-sm font-medium">
                                    {{
                                        secretStorageModeLabel(
                                            form.storage_mode,
                                        )
                                    }}
                                </p>
                            </UCard>
                            <UCard variant="outline">
                                <p
                                    class="text-xs font-medium tracking-[0.2em] text-muted uppercase"
                                >
                                    Ответственный
                                </p>
                                <p class="mt-2 text-sm font-medium">
                                    {{
                                        form.owner_user_id
                                            ? 'Назначен'
                                            : 'Не выбран'
                                    }}
                                </p>
                            </UCard>
                        </div>

                        <div class="grid gap-3 md:grid-cols-2">
                            <UAlert
                                color="primary"
                                variant="soft"
                                icon="i-lucide-badge-check"
                                title="После сохранения"
                                :description="storageSummary"
                            />

                            <UCard variant="outline">
                                <p
                                    class="text-xs font-medium tracking-[0.2em] text-muted uppercase"
                                >
                                    Сигналы доверия
                                </p>
                                <ul class="mt-3 space-y-2 text-sm text-muted">
                                    <li>
                                        Значение не показывается в листингах и
                                        не участвует в глобальном поиске.
                                    </li>
                                    <li>
                                        По умолчанию подставляется текущий
                                        ответственный, чтобы форму можно было
                                        заполнить за один проход.
                                    </li>
                                    <li>
                                        Сложные настройки спрятаны, чтобы не
                                        мешать основному действию.
                                    </li>
                                </ul>
                            </UCard>
                        </div>
                    </div>
                </UCard>
            </section>

            <section v-if="props.can_manage" class="grid gap-6">
                <UCard>
                    <template #header>
                        <div class="space-y-2">
                            <p
                                class="text-xs font-medium tracking-[0.24em] text-muted uppercase"
                            >
                                Быстрое добавление
                            </p>
                            <h2 class="text-xl font-semibold tracking-tight">
                                Где лежит секрет и кто за него отвечает
                            </h2>
                            <p class="max-w-2xl text-sm text-muted">
                                Один проход по форме: где используется секрет,
                                что именно сохраняем и кто за это отвечает.
                            </p>
                        </div>
                    </template>

                    <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
                        <UCard variant="subtle">
                            <template #header>
                                <div class="space-y-1">
                                    <h3 class="text-lg font-semibold">
                                        Контекст секрета
                                    </h3>
                                    <p class="text-sm text-muted">
                                        Где используется секрет и кто отвечает
                                        за его актуальность.
                                    </p>
                                </div>
                            </template>

                            <div class="space-y-5">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <UFormField
                                        label="Проект"
                                        required
                                        :error="form.errors.project_id"
                                    >
                                        <USelectMenu
                                            v-model="form.project_id"
                                            value-key="value"
                                            class="w-full"
                                            :items="props.project_options"
                                        />
                                    </UFormField>

                                    <UFormField
                                        label="Окружение"
                                        required
                                        :error="form.errors.environment_id"
                                    >
                                        <USelectMenu
                                            v-model="form.environment_id"
                                            value-key="value"
                                            class="w-full"
                                            :items="filteredEnvironmentOptions"
                                        />
                                    </UFormField>
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <UFormField
                                        label="Название"
                                        required
                                        :error="form.errors.name"
                                    >
                                        <UInput
                                            v-model="form.name"
                                            class="w-full"
                                            placeholder="Stripe production API key"
                                        />
                                    </UFormField>

                                    <UFormField
                                        label="Что это за секрет"
                                        required
                                        :error="form.errors.secret_type"
                                    >
                                        <USelectMenu
                                            v-model="form.secret_type"
                                            value-key="value"
                                            class="w-full"
                                            :items="secretTypeOptions"
                                            create-item
                                            @create="createSecretType"
                                        />
                                    </UFormField>
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <UFormField
                                        label="Сервис или ресурс"
                                        :error="form.errors.resource_id"
                                    >
                                        <USelectMenu
                                            v-model="form.resource_id"
                                            value-key="value"
                                            class="w-full"
                                            :items="props.resource_options"
                                            placeholder="Необязательно"
                                        />
                                    </UFormField>

                                    <UFormField
                                        label="Ответственный"
                                        :error="form.errors.owner_user_id"
                                    >
                                        <USelectMenu
                                            v-model="form.owner_user_id"
                                            value-key="value"
                                            class="w-full"
                                            :items="props.user_options"
                                            placeholder="Кто отвечает за актуальность"
                                        />
                                    </UFormField>
                                </div>
                            </div>

                            <UCollapsible
                                v-model:open="showAdvanced"
                                class="mt-4 flex flex-col gap-3"
                            >
                                <UButton
                                    :label="
                                        showAdvanced
                                            ? 'Скрыть дополнительные настройки'
                                            : 'Показать дополнительные настройки'
                                    "
                                    color="neutral"
                                    variant="subtle"
                                    trailing-icon="i-lucide-chevron-down"
                                    block
                                />

                                <template #content>
                                    <UCard variant="outline">
                                        <div class="grid gap-4 md:grid-cols-2">
                                            <UFormField
                                                label="Чувствительность"
                                                :error="form.errors.sensitivity"
                                            >
                                                <USelect
                                                    v-model="form.sensitivity"
                                                    class="w-full"
                                                    :items="sensitivityOptions"
                                                />
                                            </UFormField>

                                            <UFormField
                                                label="Кто может раскрывать"
                                                :error="
                                                    form.errors.reveal_policy
                                                "
                                            >
                                                <USelect
                                                    v-model="form.reveal_policy"
                                                    class="w-full"
                                                    :items="revealPolicyOptions"
                                                />
                                            </UFormField>

                                            <UFormField
                                                label="Заметки"
                                                class="md:col-span-2"
                                                :error="form.errors.notes"
                                            >
                                                <UTextarea
                                                    v-model="form.notes"
                                                    class="w-full"
                                                    :rows="3"
                                                    placeholder="Что важно знать про использование или ограничения"
                                                />
                                            </UFormField>
                                        </div>
                                    </UCard>
                                </template>
                            </UCollapsible>
                        </UCard>

                        <UCard variant="subtle">
                            <template #header>
                                <div class="space-y-1">
                                    <h3 class="text-lg font-semibold">
                                        Что хранится в реестре
                                    </h3>
                                    <p class="text-sm text-muted">
                                        Выбери способ хранения и сразу
                                        зафиксируй значение или внешний
                                        reference.
                                    </p>
                                </div>
                            </template>

                            <div class="space-y-6">
                                <UFormField
                                    help="Если вставляешь реальное значение прямо сюда, оно будет зашифровано при сохранении. Если секрет уже живёт во внешнем vault, фиксируй только ссылку или путь."
                                >
                                    <URadioGroup
                                        v-model="form.storage_mode"
                                        variant="card"
                                        indicator="end"
                                        orientation="horizontal"
                                        :ui="{ item: 'flex-1' }"
                                        :items="
                                            storageModeOptions.map(
                                                (option) => ({
                                                    label: option.title,
                                                    value: option.value,
                                                }),
                                            )
                                        "
                                        @update:model-value="
                                            handleStorageModeChange
                                        "
                                    />
                                </UFormField>

                                <UAlert
                                    :color="
                                        isEncryptedMode ? 'success' : 'info'
                                    "
                                    variant="soft"
                                    :icon="
                                        isEncryptedMode
                                            ? 'i-lucide-lock-keyhole'
                                            : 'i-lucide-link-2'
                                    "
                                    :title="
                                        isEncryptedMode
                                            ? 'Сырое значение будет зашифровано'
                                            : 'В реестр попадет только reference'
                                    "
                                    :description="storageSummary"
                                />

                                <UCard variant="outline">
                                    <div v-if="isEncryptedMode">
                                        <UFormField
                                            label="Сырое значение секрета"
                                            required
                                            :error="form.errors.encrypted_value"
                                        >
                                            <UTextarea
                                                v-model="form.encrypted_value"
                                                class="w-full"
                                                :rows="5"
                                                placeholder="Вставьте пароль, token, API key или другое сырое значение"
                                            />
                                        </UFormField>
                                        <p class="mt-2 text-xs text-muted">
                                            Это поле подходит для реального
                                            значения. После сохранения команда
                                            увидит только факт наличия секрета,
                                            а не сам текст.
                                        </p>
                                    </div>

                                    <div v-else>
                                        <UFormField
                                            label="Ссылка или путь во внешнем хранилище"
                                            required
                                            :error="
                                                form.errors.external_reference
                                            "
                                        >
                                            <UInput
                                                v-model="
                                                    form.external_reference
                                                "
                                                class="w-full"
                                                placeholder="vault://prod/stripe/api-key или ссылка на 1Password item"
                                            />
                                        </UFormField>
                                        <p class="mt-2 text-xs text-muted">
                                            Укажите понятный reference, по
                                            которому команда быстро найдет
                                            источник истины без поиска по чатам.
                                        </p>
                                    </div>
                                </UCard>
                            </div>
                        </UCard>
                    </div>

                    <template #footer>
                        <div
                            class="flex flex-wrap items-center justify-between gap-3"
                        >
                            <p class="max-w-xl text-sm text-muted">
                                Цель формы простая: чтобы владелец без лишних
                                вопросов понял, куда вставить значение или
                                ссылку.
                            </p>

                            <UButton
                                icon="i-lucide-shield-check"
                                :loading="form.processing"
                                :disabled="form.processing"
                                @click="createSecret"
                            >
                                Сохранить секрет
                            </UButton>
                        </div>
                    </template>
                </UCard>
            </section>

            <UEmpty
                v-if="props.secrets.length === 0"
                icon="i-lucide-shield-ellipsis"
                title="Секретов пока нет"
                description="Добавь первый секрет, чтобы команда перестала искать его по чатам и заметкам."
            />

            <section v-else>
                <div class="mb-4 flex items-center justify-between gap-4">
                    <h2 class="text-lg font-semibold">
Реестр секретов
</h2>

                    <UInput
                        v-model="secretSearch"
                        icon="i-lucide-search"
                        placeholder="Поиск по названию, проекту или окружению"
                        class="w-full max-w-sm"
                    />
                </div>

                <div class="grid gap-4 xl:grid-cols-3">
                    <UCard
                        v-for="secret in filteredSecrets"
                        :key="secret.id"
                        variant="outline"
                    >
                        <template #header>
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h2 class="text-lg font-semibold">
                                        {{ secret.name }}
                                    </h2>
                                    <p class="text-sm text-muted">
                                        {{
                                            secret.secret_type.replaceAll(
                                                '_',
                                                ' ',
                                            )
                                        }}
                                    </p>
                                </div>
                                <UBadge variant="soft" color="neutral">
                                    {{ sensitivityLabel(secret.sensitivity) }}
                                </UBadge>
                            </div>
                        </template>

                        <div class="space-y-2 text-sm text-muted">
                            <p>
                                {{ secret.project_name }} /
                                {{ secret.environment_name }}
                            </p>
                            <p>
                                Ответственный:
                                {{ secret.owner_user_name || 'не назначен' }}
                            </p>
                            <p>
                                Хранение:
                                {{
                                    secret.storage_mode === 'encrypted_value'
                                        ? 'значение внутри Access Atlas'
                                        : secret.external_reference ||
                                          secretStorageModeLabel(
                                              secret.storage_mode,
                                          )
                                }}
                            </p>
                            <p>
                                Статус: {{ projectStatusLabel(secret.status) }}
                            </p>
                            <p>Потребителей: {{ secret.consumers.length }}</p>
                        </div>

                        <template #footer>
                            <UButton
                                v-if="props.can_reveal"
                                variant="soft"
                                color="warning"
                                icon="i-lucide-eye"
                                @click="startReveal(secret)"
                            >
                                Раскрыть
                            </UButton>
                        </template>
                    </UCard>
                </div>

                <p
                    v-if="filteredSecrets.length === 0 && secretSearch"
                    class="py-4 text-center text-sm text-muted"
                >
                    Ничего не найдено по запросу «{{ secretSearch }}»
                </p>
            </section>

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
