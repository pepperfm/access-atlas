<script setup lang="ts">
import type { BreadcrumbItem } from '@/types'
import type { ResourcesIndexPageProps } from '@/types/resources'
import { Head, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { route } from 'ziggy-js'
import { useFormErrorToast } from '@/composables/useFormErrorToast'
import AppLayout from '@/layouts/AppLayout.vue'
import { projectStatusLabel, sensitivityLabel } from '@/lib/labels'
import { dashboard } from '@/routes'

const props = defineProps<ResourcesIndexPageProps>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Дашборд', href: dashboard() },
  { title: 'Ресурсы', href: route('resources.index') },
]

const form = useForm({
  provider: '',
  kind: '',
  name: '',
  external_identifier: '',
  owner_user_id: '',
  sensitivity: 'medium',
  canonical_source: '',
  notes: '',
})

useFormErrorToast(computed(() => form.errors), 'Не удалось создать ресурс')

function createResource(): void {
  form.post(route('resources.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      form.sensitivity = 'medium'
    },
  })
}
</script>

<template>
  <Head title="Ресурсы" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-1 flex-col gap-6 p-4">
      <UPageCard
        title="Ресурсы"
        description="Заводи здесь понятные сущности вроде Stripe, GitHub, Sentry или AWS-аккаунта. Без лишней терминологии."
      />

      <UCard v-if="props.can_manage">
        <template #header>
          <div class="space-y-1">
            <h2 class="text-lg font-semibold">
              Новый ресурс
            </h2>
            <p class="text-sm text-muted">
              Нужен только сервис, что это за сущность, её имя и кто за неё отвечает.
            </p>
          </div>
        </template>

        <UForm
          :state="form"
          class="grid gap-4 md:grid-cols-2"
          @submit.prevent="createResource"
        >
          <UFormField
            label="Сервис"
            required
            :error="form.errors.provider"
          >
            <UInput
              v-model="form.provider"
              class="w-full"
              placeholder="Stripe"
            />
          </UFormField>

          <UFormField
            label="Что именно"
            required
            :error="form.errors.kind"
          >
            <UInput
              v-model="form.kind"
              class="w-full"
              placeholder="Аккаунт, workspace, репозиторий"
            />
          </UFormField>

          <UFormField
            label="Название"
            required
            :error="form.errors.name"
          >
            <UInput
              v-model="form.name"
              class="w-full"
              placeholder="Payments production"
            />
          </UFormField>

          <UFormField
            label="Ответственный"
            required
            :error="form.errors.owner_user_id"
          >
            <USelectMenu
              v-model="form.owner_user_id"
              value-key="value"
              class="w-full"
              :items="props.user_options"
              placeholder="Кто отвечает за ресурс"
            />
          </UFormField>

          <UFormField label="ID или ссылка" :error="form.errors.external_identifier">
            <UInput
              v-model="form.external_identifier"
              class="w-full"
              placeholder="acct_..., repo URL, workspace slug"
            />
          </UFormField>

          <UFormField label="Комментарий" :error="form.errors.notes">
            <UInput
              v-model="form.notes"
              class="w-full"
              placeholder="Например: основной платёжный аккаунт."
            />
          </UFormField>

          <div class="md:col-span-2">
            <UCollapsible class="flex flex-col gap-3">
              <UButton
                label="Дополнительные настройки"
                color="neutral"
                variant="subtle"
                trailing-icon="i-lucide-chevron-down"
                block
              />

              <template #content>
                <UCard variant="subtle">
                  <div class="grid gap-4 md:grid-cols-2">
                    <UFormField label="Чувствительность" :error="form.errors.sensitivity">
                      <USelect
                        v-model="form.sensitivity"
                        class="w-full"
                        :items="[
                          { label: 'Низкая', value: 'low' },
                          { label: 'Средняя', value: 'medium' },
                          { label: 'Высокая', value: 'high' },
                          { label: 'Критическая', value: 'critical' },
                        ]"
                      />
                    </UFormField>

                    <UFormField label="Источник истины" :error="form.errors.canonical_source">
                      <UInput
                        v-model="form.canonical_source"
                        class="w-full"
                        placeholder="Где эту сущность обычно администрируют"
                      />
                    </UFormField>
                  </div>
                </UCard>
              </template>
            </UCollapsible>
          </div>

          <div class="md:col-span-2 flex items-center justify-between gap-3">
            <UAlert
              class="flex-1"
              color="info"
              variant="soft"
              icon="i-lucide-link-2"
              title="Проекты привязываются позже"
              description="Сначала можно просто завести ресурс, а потом уже связать его с нужным проектом."
            />

            <UButton
              :loading="form.processing"
              icon="i-lucide-plus"
              type="submit"
            >
              Создать ресурс
            </UButton>
          </div>
        </UForm>
      </UCard>

      <UEmpty
        v-if="props.resources.length === 0"
        icon="i-lucide-server-cog"
        title="Ресурсов пока нет"
        description="Заведи первый ресурс, чтобы потом выдавать к нему доступы и привязывать секреты."
      />

      <section v-else class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <UCard
          v-for="resource in props.resources"
          :key="resource.id"
        >
          <template #header>
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="text-xs uppercase tracking-[0.2em] text-muted">
                  {{ resource.provider }}
                </p>
                <h2 class="mt-2 text-lg font-semibold">
                  {{ resource.name }}
                </h2>
              </div>

              <UBadge variant="soft" color="neutral">
                {{ projectStatusLabel(resource.status) }}
              </UBadge>
            </div>
          </template>

          <div class="space-y-2 text-sm text-muted">
            <p>{{ resource.kind }}</p>
            <p>Ответственный: {{ resource.owner_user_name }}</p>
            <p v-if="resource.external_identifier">
              {{ resource.external_identifier }}
            </p>
          </div>

          <template #footer>
            <div class="flex items-center justify-between gap-3">
              <UBadge variant="subtle" color="primary">
                {{ resource.project_links.length }} {{ resource.project_links.length === 1 ? 'проект' : 'проекта' }}
              </UBadge>
              <UBadge variant="subtle" color="neutral">
                {{ sensitivityLabel(resource.sensitivity) }}
              </UBadge>

              <UButton
                :to="route('resources.show', resource.id)"
                variant="ghost"
                icon="i-lucide-arrow-right"
              >
                Открыть
              </UButton>
            </div>
          </template>
        </UCard>
      </section>
    </div>
  </AppLayout>
</template>
