<script setup lang="ts">
import type { BreadcrumbItem } from '@/types'
import type { InboxIndexPageProps } from '@/types/inbox'
import { Head, router, useForm } from '@inertiajs/vue3'
import { computed, reactive } from 'vue'
import { route } from 'ziggy-js'
import { useFormErrorToast } from '@/composables/useFormErrorToast'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'

const props = defineProps<InboxIndexPageProps>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: dashboard() },
  { title: 'Inbox', href: route('inbox.index') },
]

const form = useForm({
  source_type: 'manual',
  raw_text: '',
  suggested_project_id: '',
})

const normalizeForms = reactive(
  Object.fromEntries(
    props.items.map(item => [
      item.id,
      useForm({
        project_id: '',
        environment_id: '',
        name: '',
        secret_type: 'api_key',
        storage_mode: 'external_reference',
        external_reference: '',
        encrypted_value: '',
        owner_user_id: '',
        reveal_policy: 'project_owner_or_security_admin',
        sensitivity: 'medium',
      }),
    ]),
  ) as Record<string, ReturnType<typeof useForm<{
    project_id: string
    environment_id: string
    name: string
    secret_type: string
    storage_mode: string
    external_reference: string
    encrypted_value: string
    owner_user_id: string
    reveal_policy: string
    sensitivity: string
  }>>>,
)

useFormErrorToast(computed(() => form.errors), 'Не удалось добавить запись в инбокс')

Object.entries(normalizeForms).forEach(([itemId, normalizeForm]) => {
  useFormErrorToast(
    computed(() => normalizeForm.errors),
    `Не удалось нормализовать запись ${itemId}`,
  )
})

function createInboxItem(): void {
  form.post(route('inbox.store'), {
    errorBag: 'createInboxItem',
  })
}

function normalize(itemId: string): void {
  const itemForm = normalizeForms[itemId]

  itemForm.transform(data => ({
    ...data,
    parsed_summary: { imported: true, source: 'manual-normalization' },
  }))

  itemForm.post(route('inbox.normalize', itemId), {
    preserveScroll: true,
    errorBag: `normalizeInboxItem.${itemId}`,
  })
}

function purge(itemId: string): void {
  router.post(route('inbox.purge', itemId))
}
</script>

<template>
  <Head title="Инбокс" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-1 flex-col gap-6 p-4">
      <section class="rounded-xl border border-default/70 bg-default p-6">
        <h1 class="text-2xl font-semibold tracking-tight">
          Инбокс
        </h1>
        <p class="mt-2 max-w-2xl text-sm text-muted">
          Сырые фрагменты чатов и ручные заметки, которые ещё нужно нормализовать в структурированные сущности.
        </p>
      </section>

      <section v-if="props.can_manage" class="rounded-xl border border-default/70 bg-default p-6">
        <UFormField label="Предполагаемый проект" :error="form.errors.suggested_project_id">
          <USelectMenu
            v-model="form.suggested_project_id"
            value-key="value"
            class="w-full"
            :items="props.project_options"
          />
        </UFormField>
        <UFormField label="Сырой текст" :error="form.errors.raw_text">
          <UTextarea
            v-model="form.raw_text"
            class="w-full"
            :rows="5"
          />
        </UFormField>
        <div class="mt-4 flex justify-end">
          <UButton icon="i-lucide-inbox" @click="createInboxItem">
            Добавить в инбокс
          </UButton>
        </div>
      </section>

      <section class="grid gap-4 xl:grid-cols-3">
        <UCard
          v-for="item in props.items"
          :key="item.id"
          class="border border-default/70"
        >
          <template #header>
            <div class="flex items-start justify-between gap-4">
              <div>
                <h2 class="text-lg font-semibold">
                  {{ item.source_type }}
                </h2>
                <p class="text-sm text-muted">
                  {{ item.status }}
                </p>
              </div>
            </div>
          </template>

          <p class="line-clamp-4 text-sm text-muted">
            {{ item.raw_text || 'Сырой текст уже очищен.' }}
          </p>

          <template #footer>
            <div class="grid gap-4">
              <div class="grid gap-4 md:grid-cols-2">
                <UFormField label="Проект">
                  <USelectMenu
                    v-model="normalizeForms[item.id].project_id"
                    value-key="value"
                    class="w-full"
                    :items="props.project_options"
                  />
                </UFormField>
                <UFormField label="Окружение">
                  <USelectMenu
                    v-model="normalizeForms[item.id].environment_id"
                    value-key="value"
                    class="w-full"
                    :items="props.environment_options"
                  />
                </UFormField>
                <UFormField label="Название секрета">
                  <UInput
                    v-model="normalizeForms[item.id].name"
                    class="w-full"
                    placeholder="Имя импортированного секрета"
                  />
                </UFormField>
                <UFormField label="Тип секрета">
                  <UInput
                    v-model="normalizeForms[item.id].secret_type"
                    class="w-full"
                    placeholder="api_key"
                  />
                </UFormField>
                <UFormField label="Владелец">
                  <USelectMenu
                    v-model="normalizeForms[item.id].owner_user_id"
                    value-key="value"
                    class="w-full"
                    :items="props.user_options"
                  />
                </UFormField>
                <UFormField label="Режим хранения">
                  <USelect
                    v-model="normalizeForms[item.id].storage_mode"
                    class="w-full"
                    :items="[
                      { label: 'Внешняя ссылка', value: 'external_reference' },
                      { label: 'Зашифрованное значение', value: 'encrypted_value' },
                    ]"
                  />
                </UFormField>
              </div>
              <div class="flex gap-2">
                <UButton
                  variant="soft"
                  icon="i-lucide-wand"
                  @click="normalize(item.id)"
                >
                  Нормализовать
                </UButton>
                <UButton
                  variant="soft"
                  color="error"
                  icon="i-lucide-eraser"
                  @click="purge(item.id)"
                >
                  Очистить сырой текст
                </UButton>
              </div>
            </div>
          </template>
        </UCard>
      </section>
    </div>
  </AppLayout>
</template>
