<script setup lang="ts">
import type { BreadcrumbItem } from '@/types'
import type { AuditIndexPageProps } from '@/types/operations'
import { Head } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'

const props = defineProps<AuditIndexPageProps>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Дашборд', href: dashboard() },
  { title: 'Аудит', href: route('audit.index') },
]
</script>

<template>
  <Head title="Аудит" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-1 flex-col gap-4 p-4">
      <section class="rounded-xl border border-default/70 bg-default p-6">
        <h1 class="text-2xl font-semibold tracking-tight">
          Журнал аудита
        </h1>
        <p class="mt-2 text-sm text-muted">
          История security-relevant действий: смены ролей, раскрытия, связи ресурсов и жизненный цикл доступов.
        </p>
      </section>

      <UCard
        v-for="event in props.events"
        :key="event.id"
        class="border border-default/70"
      >
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="font-medium">
              {{ event.action }}
            </p>
            <p class="text-sm text-muted">
              {{ event.target_type }} · {{ event.target_id }}
            </p>
            <p class="text-sm text-muted">
              {{ event.project_name || 'Без привязки к проекту' }}
            </p>
          </div>
          <div class="text-right text-sm text-muted">
            <p>{{ event.actor_name || 'система' }}</p>
            <p>{{ event.created_at || 'н/д' }}</p>
          </div>
        </div>
      </UCard>
    </div>
  </AppLayout>
</template>
