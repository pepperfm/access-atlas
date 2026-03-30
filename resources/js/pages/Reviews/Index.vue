<script setup lang="ts">
import type { BreadcrumbItem } from '@/types'
import type { ReviewsIndexPageProps } from '@/types/reviews'
import { Head, router, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { route } from 'ziggy-js'
import { useFormErrorToast } from '@/composables/useFormErrorToast'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'

const props = defineProps<ReviewsIndexPageProps>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: dashboard() },
  { title: 'Reviews', href: route('reviews.index') },
]

const form = useForm({
  target_type: 'secret',
  target_id: '',
  task_type: 'secret_verification',
  assigned_to_user_id: '',
  due_at: '',
  comment: '',
})

useFormErrorToast(computed(() => form.errors), 'Не удалось создать задачу ревью')

const targetItems = computed(() => props.target_options[form.target_type] ?? [])

function createTask(): void {
  form.post(route('reviews.store'))
}

function completeTask(taskId: string): void {
  router.post(route('reviews.complete', taskId), {
    result: 'confirmed',
    comment: 'Completed from review queue.',
  })
}
</script>

<template>
  <Head title="Ревью" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-1 flex-col gap-6 p-4">
      <section class="rounded-xl border border-default/70 bg-default p-6">
        <h1 class="text-2xl font-semibold tracking-tight">
          Ревью
        </h1>
        <p class="mt-2 max-w-2xl text-sm text-muted">
          Очередь ротации, проверки и ownership review для чувствительных объектов.
        </p>
      </section>

      <section v-if="props.can_manage" class="rounded-xl border border-default/70 bg-default p-6">
        <div class="grid gap-4 md:grid-cols-2">
          <UFormField label="Тип объекта" :error="form.errors.target_type">
            <USelect
              v-model="form.target_type"
              class="w-full"
              :items="[
                { label: 'Секрет', value: 'secret' },
                { label: 'Доступ', value: 'access_grant' },
                { label: 'Ресурс', value: 'resource' },
              ]"
            />
          </UFormField>
          <UFormField label="Объект" :error="form.errors.target_id">
            <USelectMenu
              v-model="form.target_id"
              value-key="value"
              class="w-full"
              :items="targetItems"
            />
          </UFormField>
          <UFormField label="Тип задачи" :error="form.errors.task_type">
            <USelect
              v-model="form.task_type"
              class="w-full"
              :items="[
                { label: 'Ревью доступа', value: 'access_review' },
                { label: 'Проверка секрета', value: 'secret_verification' },
                { label: 'Ротация секрета', value: 'secret_rotation' },
                { label: 'Проверка ownership', value: 'ownership_review' },
              ]"
            />
          </UFormField>
          <UFormField label="Ответственный" :error="form.errors.assigned_to_user_id">
            <USelectMenu
              v-model="form.assigned_to_user_id"
              value-key="value"
              class="w-full"
              :items="props.assignee_options"
            />
          </UFormField>
          <UFormField label="Срок" :error="form.errors.due_at">
            <UInput
              v-model="form.due_at"
              class="w-full"
              type="datetime-local"
            />
          </UFormField>
        </div>
        <div class="mt-4 flex justify-end">
          <UButton icon="i-lucide-plus" @click="createTask">
            Создать задачу ревью
          </UButton>
        </div>
      </section>

      <section class="grid gap-4 xl:grid-cols-3">
        <UCard
          v-for="task in props.tasks"
          :key="task.id"
          class="border border-default/70"
        >
          <template #header>
            <div class="flex items-start justify-between gap-4">
              <div>
                <h2 class="text-lg font-semibold">
                  {{ task.task_type }}
                </h2>
                <p class="text-sm text-muted">
                  {{ task.target_type }} / {{ task.target_id }}
                </p>
              </div>
              <UBadge variant="soft" color="neutral">
                {{ task.status }}
              </UBadge>
            </div>
          </template>

          <p class="text-sm text-muted">
            Срок: {{ task.due_at }}
          </p>

          <template #footer>
            <UButton
              v-if="props.can_manage && task.status !== 'completed'"
              variant="soft"
              color="success"
              icon="i-lucide-check"
              @click="completeTask(task.id)"
            >
              Завершить
            </UButton>
          </template>
        </UCard>
      </section>
    </div>
  </AppLayout>
</template>
