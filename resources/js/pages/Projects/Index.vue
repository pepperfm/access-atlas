<script setup lang="ts">
import type { BreadcrumbItem, ProjectsIndexPageProps } from '@/types'
import { Head, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { route } from 'ziggy-js'
import { useFormErrorToast } from '@/composables/useFormErrorToast'
import AppLayout from '@/layouts/AppLayout.vue'
import { criticalityLabel, projectStatusLabel } from '@/lib/labels'
import { dashboard } from '@/routes'

const props = defineProps<ProjectsIndexPageProps>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Дашборд', href: dashboard() },
  { title: 'Проекты', href: route('projects.index') },
]

const form = useForm({
  name: '',
  repository_url: '',
  description: '',
  criticality: 'medium',
})

useFormErrorToast(computed(() => form.errors), 'Не удалось создать проект')

function submit(): void {
  form.post(route('projects.store'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Проекты" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-1 flex-col gap-6 p-4">
      <UPageCard
        title="Проекты"
        description="Минимальная карточка проекта: название, репозиторий, критичность и команда."
      />

      <UCard v-if="props.can_create">
        <template #header>
          <div class="space-y-1">
            <h2 class="text-lg font-semibold">
              Новый проект
            </h2>
            <p class="text-sm text-muted">
              Ключ система создаст сама из названия. Тебе нужен только смысл проекта.
            </p>
          </div>
        </template>

        <UForm
          :state="form"
          class="grid gap-4 md:grid-cols-2"
          @submit.prevent="submit"
        >
          <UFormField
            label="Название проекта"
            name="name"
            required
            :error="form.errors.name"
          >
            <UInput
              v-model="form.name"
              class="w-full"
              placeholder="Access Atlas"
            />
          </UFormField>

          <UFormField
            label="Ссылка на репозиторий"
            name="repository_url"
            :error="form.errors.repository_url"
          >
            <UInput
              v-model="form.repository_url"
              class="w-full"
              placeholder="https://github.com/pepperfm/access-atlas"
            />
          </UFormField>

          <UFormField
            label="Критичность"
            name="criticality"
            :error="form.errors.criticality"
          >
            <USelect
              v-model="form.criticality"
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
            :error="form.errors.description"
          >
            <UTextarea
              v-model="form.description"
              class="w-full"
              :rows="4"
              placeholder="Что проектом владеет, с чем он интегрируется и что нужно защищать."
            />
          </UFormField>

          <div class="md:col-span-2 flex justify-end">
            <UButton
              :loading="form.processing"
              icon="i-lucide-plus"
              type="submit"
            >
              Создать проект
            </UButton>
          </div>
        </UForm>
      </UCard>

      <UEmpty
        v-if="props.projects.length === 0"
        icon="i-lucide-folder-open-dot"
        title="Проектов пока нет"
        description="Создай первый проект, чтобы затем привязывать к нему команду, ресурсы, доступы и секреты."
      />

      <section v-else class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <UCard
          v-for="project in props.projects"
          :key="project.id"
        >
          <template #header>
            <div class="flex items-start justify-between gap-4">
              <div>
                <h2 class="text-lg font-semibold">
                  {{ project.name }}
                </h2>
                <p class="mt-1 text-xs uppercase tracking-[0.24em] text-muted">
                  {{ project.key }}
                </p>
              </div>
              <UBadge variant="soft" color="neutral">
                {{ criticalityLabel(project.criticality) }}
              </UBadge>
            </div>
          </template>

          <p class="text-sm text-muted">
            {{ project.description || 'Описание проекта пока не заполнено.' }}
          </p>

          <p v-if="project.repository_url" class="mt-3 text-sm">
            <ULink
              :to="project.repository_url"
              target="_blank"
              class="text-primary"
            >
              Открыть репозиторий
            </ULink>
          </p>

          <div class="mt-4 flex flex-wrap gap-2 text-xs text-muted">
            <UBadge variant="subtle" color="primary">
              {{ project.environments.length }} окружений
            </UBadge>
            <UBadge variant="subtle" color="neutral">
              {{ project.memberships.length }} участников
            </UBadge>
            <UBadge variant="subtle" color="neutral">
              {{ projectStatusLabel(project.status) }}
            </UBadge>
          </div>

          <template #footer>
            <UButton
              :to="route('projects.show', project.id)"
              variant="ghost"
              icon="i-lucide-arrow-right"
            >
              Открыть проект
            </UButton>
          </template>
        </UCard>
      </section>
    </div>
  </AppLayout>
</template>
