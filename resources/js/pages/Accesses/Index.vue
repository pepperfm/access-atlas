<script setup lang="ts">
import type { BreadcrumbItem } from '@/types'
import type { AccessIndexPageProps } from '@/types/access'
import type { DateValue } from '@internationalized/date'
import { Head, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { route } from 'ziggy-js'
import { useFormErrorToast } from '@/composables/useFormErrorToast'
import AppLayout from '@/layouts/AppLayout.vue'
import { accessLevelLabel, grantKindLabel, projectStatusLabel } from '@/lib/labels'
import { dashboard } from '@/routes'

const props = defineProps<AccessIndexPageProps>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Дашборд', href: dashboard() },
  { title: 'Доступы', href: route('access-grants.index') },
]

const form = useForm({
  project_id: '',
  environment_id: '',
  resource_id: '',
  user_id: '',
  grant_kind: 'credential',
  access_level: 'read',
  scope_description: '',
  owner_user_id: '',
  starts_at: '',
  expires_at: '',
  review_due_at: '',
  notes: '',
})

const expiresAt = ref<DateValue | null>(null)
const reviewDueAt = ref<DateValue | null>(null)

useFormErrorToast(computed(() => form.errors), 'Не удалось сохранить доступ')

const filteredEnvironmentOptions = computed(() =>
  props.environment_options.filter(environment =>
    !form.project_id || environment.project_id === form.project_id,
  ),
)

const grantKindItems = [
  { label: 'Логин и пароль', value: 'credential' },
  { label: 'Токен', value: 'token' },
  { label: 'Роль', value: 'role' },
  { label: 'Владелец', value: 'ownership' },
]

const accessLevelItems = [
  { label: 'Только чтение', value: 'read' },
  { label: 'Изменение', value: 'write' },
  { label: 'Админ', value: 'admin' },
  { label: 'Владелец', value: 'owner' },
]

function createGrant(): void {
  form.transform(data => ({
    ...data,
    expires_at: expiresAt.value?.toString() ?? null,
    review_due_at: reviewDueAt.value?.toString() ?? null,
  }))

  form.post(route('access-grants.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      form.grant_kind = 'credential'
      form.access_level = 'read'
      expiresAt.value = null
      reviewDueAt.value = null
    },
  })
}

function revokeGrant(accessGrantId: string): void {
  form.post(route('access-grants.revoke', accessGrantId), {
    preserveScroll: true,
  })
}


</script>

<template>
  <Head title="Доступы" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-1 flex-col gap-6 p-4">
      <UPageCard
        title="Доступы"
        description="Фиксируй здесь, кому именно выдали доступ, к чему и кто за него отвечает."
      />

      <UCard v-if="props.can_manage">
        <template #header>
          <div class="space-y-1">
            <h2 class="text-lg font-semibold">
              Новый доступ
            </h2>
            <p class="text-sm text-muted">
              Главный вопрос формы: кому и какой доступ дали.
            </p>
          </div>
        </template>

        <UForm
          :state="form"
          class="space-y-6"
          @submit.prevent="createGrant"
        >
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

            <UFormField label="Окружение" :error="form.errors.environment_id">
              <USelectMenu
                v-model="form.environment_id"
                value-key="value"
                class="w-full"
                :items="filteredEnvironmentOptions"
                placeholder="Если доступ связан с конкретной средой"
              />
            </UFormField>

            <UFormField
              label="Ресурс"
              required
              :error="form.errors.resource_id"
            >
              <USelectMenu
                v-model="form.resource_id"
                value-key="value"
                class="w-full"
                :items="props.resource_options"
              />
            </UFormField>

            <UFormField
              label="Кому выдали"
              required
              :error="form.errors.user_id"
            >
              <USelectMenu
                v-model="form.user_id"
                value-key="value"
                class="w-full"
                :items="props.user_options"
              />
            </UFormField>

            <UFormField
              label="Кто отвечает"
              required
              :error="form.errors.owner_user_id"
            >
              <USelectMenu
                v-model="form.owner_user_id"
                value-key="value"
                class="w-full"
                :items="props.user_options"
              />
            </UFormField>

            <UFormField label="Короткое пояснение" :error="form.errors.scope_description">
              <UInput
                v-model="form.scope_description"
                class="w-full"
                placeholder="Например: деплой, платежи, аналитика"
              />
            </UFormField>
          </div>

          <div class="grid gap-4 xl:grid-cols-2">
            <UFormField
              label="Формат доступа"
              help="Что именно выдали человеку: учётку, токен, роль внутри сервиса или полный ownership."
              :error="form.errors.grant_kind"
            >
              <URadioGroup
                v-model="form.grant_kind"
                variant="card"
                indicator="end"
                orientation="horizontal"
                :ui="{ item: 'flex-1' }"
                :items="grantKindItems"
              />
            </UFormField>

            <UFormField
              label="Уровень доступа"
              help="Насколько широкие права у человека внутри этого ресурса."
              :error="form.errors.access_level"
            >
              <URadioGroup
                v-model="form.access_level"
                variant="card"
                indicator="end"
                orientation="horizontal"
                :ui="{ item: 'flex-1' }"
                :items="accessLevelItems"
              />
            </UFormField>
          </div>

          <UCollapsible class="flex flex-col gap-3">
            <UButton
              label="Дополнительные сроки и заметки"
              color="neutral"
              variant="subtle"
              trailing-icon="i-lucide-chevron-down"
              block
            />

            <template #content>
              <UCard variant="subtle">
                <div class="grid gap-4 md:grid-cols-2">
                  <UFormField label="Дата окончания" :error="form.errors.expires_at">
                    <UInputDate
                      v-model="expiresAt"
                      class="w-full"
                      icon="i-lucide-calendar-days"
                    />
                  </UFormField>

                  <UFormField label="Дата ревью" :error="form.errors.review_due_at">
                    <UInputDate
                      v-model="reviewDueAt"
                      class="w-full"
                      icon="i-lucide-calendar-check-2"
                    />
                  </UFormField>

                  <UFormField
                    label="Заметка"
                    class="md:col-span-2"
                    :error="form.errors.notes"
                  >
                    <UTextarea
                      v-model="form.notes"
                      class="w-full"
                      :rows="3"
                      placeholder="Что важно не забыть про этот доступ"
                    />
                  </UFormField>
                </div>
              </UCard>
            </template>
          </UCollapsible>

          <div class="flex items-center justify-between gap-3">
            <UAlert
              class="flex-1"
              color="info"
              variant="soft"
              icon="i-lucide-badge-check"
              title="Сначала понятность, потом детализация"
              description="Если доступ понятен без дат и лишних пояснений, необязательные поля можно не трогать."
            />

            <UButton
              :loading="form.processing"
              icon="i-lucide-plus"
              type="submit"
            >
              Сохранить доступ
            </UButton>
          </div>
        </UForm>
      </UCard>

      <UEmpty
        v-if="props.access_grants.length === 0"
        icon="i-lucide-key-round"
        title="Доступов пока нет"
        description="Добавь первый доступ, чтобы видеть кто и к чему подключён."
      />

      <section v-else class="space-y-4">
        <UPageCard
          title="Выданные доступы"
          description="Живой реестр того, кто и к какому ресурсу подключён прямо сейчас."
        />

        <div class="grid gap-4 xl:grid-cols-3">
          <UCard
          v-for="grant in props.access_grants"
          :key="grant.id"
        >
          <template #header>
            <div class="flex flex-wrap items-start justify-between gap-4">
              <div>
                <h2 class="text-lg font-semibold">
                  {{ grant.user_name }} → {{ grant.resource_name }}
                </h2>
                <p class="mt-2 text-sm text-muted">
                  {{ grant.project_name }}
                  <span v-if="grant.environment_name">· {{ grant.environment_name }}</span>
                  <span v-if="grant.scope_description">· {{ grant.scope_description }}</span>
                </p>
              </div>

              <div class="flex items-center gap-2">
                <UBadge variant="soft" color="primary">
                  {{ accessLevelLabel(grant.access_level) }}
                </UBadge>
                <UBadge variant="soft" color="neutral">
                  {{ grantKindLabel(grant.grant_kind) }}
                </UBadge>
                <UBadge variant="soft" color="neutral">
                  {{ projectStatusLabel(grant.status) }}
                </UBadge>
              </div>
            </div>
          </template>

          <div class="flex flex-wrap gap-2 text-xs text-muted">
            <UBadge variant="subtle" color="neutral">
              отвечает: {{ grant.owner_user_name }}
            </UBadge>
            <UBadge
              v-if="grant.expires_at"
              variant="subtle"
              color="warning"
            >
              истекает
            </UBadge>
            <UBadge
              v-if="grant.review_due_at"
              variant="subtle"
              color="warning"
            >
              нужен review
            </UBadge>
            <UBadge
              v-if="grant.revoked_at"
              variant="subtle"
              color="error"
            >
              отозван
            </UBadge>
          </div>

          <template #footer>
            <UButton
              v-if="props.can_manage && grant.status !== 'revoked'"
              variant="soft"
              color="error"
              icon="i-lucide-shield-off"
              @click="revokeGrant(grant.id)"
            >
              Отозвать
            </UButton>
          </template>
          </UCard>
        </div>
      </section>
    </div>
  </AppLayout>
</template>
