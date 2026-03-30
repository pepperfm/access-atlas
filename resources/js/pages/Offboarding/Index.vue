<script setup lang="ts">
import type { BreadcrumbItem } from '@/types'
import type {
  OffboardingChecklistItem,
  OffboardingPageProps,
} from '@/types/operations'
import { Head, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'

const props = defineProps<OffboardingPageProps>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Дашборд', href: dashboard() },
  { title: 'Оффбординг', href: route('offboarding.index') },
]

function selectParticipant(userId: string): void {
  router.get(route('offboarding.index'), { user_id: userId }, {
    preserveState: true,
    preserveScroll: true,
  })
}

function listTitle(items: OffboardingChecklistItem[]): string {
  return items.length === 0 ? 'Нет открытых пунктов' : `${items.length} открытых пунктов`
}
</script>

<template>
  <Head title="Оффбординг" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-1 flex-col gap-6 p-4">
      <section class="rounded-xl border border-default/70 bg-default p-6">
        <h1 class="text-2xl font-semibold tracking-tight">
          Консоль оффбординга
        </h1>
        <p class="mt-2 text-sm text-muted">
          User-centric чеклист по membership, доступам, ресурсам, секретам и назначенным ревью.
        </p>
        <USelectMenu
          class="mt-4 w-full"
          value-key="value"
          :items="props.users.map((user) => ({
            label: `${user.name} (${user.status})`,
            value: user.id,
          }))"
          @update:model-value="selectParticipant(String($event))"
        />
      </section>

      <section v-if="props.summary" class="grid gap-4 xl:grid-cols-2">
        <UCard class="border border-default/70">
          <template #header>
            <div>
              <h2 class="text-lg font-semibold">
                {{ props.summary.user_name }}
              </h2>
              <p class="text-sm text-muted">
                {{ props.summary.status }}
              </p>
            </div>
          </template>

          <div class="space-y-4">
            <div>
              <p class="font-medium">
                Участие в проектах
              </p>
              <p class="text-sm text-muted">
                {{ listTitle(props.summary.memberships) }}
              </p>
            </div>
            <div>
              <p class="font-medium">
                Доступы
              </p>
              <p class="text-sm text-muted">
                {{ listTitle(props.summary.access_grants) }}
              </p>
            </div>
            <div>
              <p class="font-medium">
                Ресурсы во владении
              </p>
              <p class="text-sm text-muted">
                {{ listTitle(props.summary.owned_resources) }}
              </p>
            </div>
            <div>
              <p class="font-medium">
                Секреты во владении
              </p>
              <p class="text-sm text-muted">
                {{ listTitle(props.summary.owned_secrets) }}
              </p>
            </div>
            <div>
              <p class="font-medium">
                Назначенные ревью
              </p>
              <p class="text-sm text-muted">
                {{ listTitle(props.summary.assigned_reviews) }}
              </p>
            </div>
          </div>
        </UCard>

        <UCard class="border border-default/70">
          <template #header>
            <h2 class="text-lg font-semibold">
              Чеклист
            </h2>
          </template>

          <div class="space-y-3">
            <div
              v-for="item in [
                ...props.summary.memberships,
                ...props.summary.access_grants,
                ...props.summary.owned_resources,
                ...props.summary.owned_secrets,
                ...props.summary.assigned_reviews,
              ]"
              :key="`${item.href}:${item.title}`"
              class="rounded-lg border border-default/60 px-4 py-3"
            >
              <p class="font-medium">
                {{ item.title }}
              </p>
              <p class="text-sm text-muted">
                {{ item.description }}
              </p>
              <UButton
                as-child
                variant="ghost"
                class="mt-2"
                icon="i-lucide-arrow-right"
              >
                <a :href="item.href">Открыть объект</a>
              </UButton>
            </div>
          </div>
        </UCard>
      </section>
    </div>
  </AppLayout>
</template>
