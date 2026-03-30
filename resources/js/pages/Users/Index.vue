<script setup lang="ts">
import type { BreadcrumbItem, UsersIndexPageProps } from '@/types'
import { Head, useForm } from '@inertiajs/vue3'
import { computed, reactive, ref } from 'vue'
import { route } from 'ziggy-js'
import { useFormErrorToast } from '@/composables/useFormErrorToast'
import AppLayout from '@/layouts/AppLayout.vue'
import { appRoleLabel, userStatusLabel } from '@/lib/labels'
import { dashboard } from '@/routes'

const props = defineProps<UsersIndexPageProps>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Дашборд', href: dashboard() },
  { title: 'Пользователи', href: route('users.index') },
]

const search = ref('')

const filteredUsers = computed(() => {
  const query = search.value.toLowerCase().trim()

  if (!query) {
    return props.users
  }

  return props.users.filter(user =>
    user.name.toLowerCase().includes(query)
    || user.email.toLowerCase().includes(query),
  )
})

const createForm = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'developer',
  status: 'active',
})

useFormErrorToast(computed(() => createForm.errors), 'Не удалось создать пользователя')

const updateForms = reactive(
  Object.fromEntries(
    props.users.map(user => [
      user.id,
      useForm({
        role: user.role || 'developer',
        status: user.status,
      }),
    ]),
  ) as Record<string, ReturnType<typeof useForm<{ role: string, status: string }>>>,
)

Object.entries(updateForms).forEach(([userId, form]) => {
  const managedUser = props.users.find(user => user.id === userId)

  useFormErrorToast(
    computed(() => form.errors),
    managedUser ? `Не удалось обновить пользователя ${managedUser.email}` : 'Не удалось обновить пользователя',
  )
})

function createUser(): void {
  createForm.post(route('users.store'), {
    preserveScroll: true,
    onSuccess: () => {
      const toast = useToast()

      toast.add({
        color: 'success',
        title: 'Пользователь создан',
        description: 'Теперь добавьте его в проект, чтобы он получил доступ к секретам.',
        actions: [
          {
            label: 'Перейти к проектам',
            click: () => {
              window.location.href = route('projects.index')
            },
          },
        ],
      })

      createForm.reset()
      createForm.role = 'developer'
      createForm.status = 'active'
    },
  })
}

function updateUserRole(userId: string, role: string, status: string): void {
  const form = updateForms[userId]

  if (!form) {
    return
  }

  form.role = role
  form.status = status

  form.put(route('users.update', userId), {
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Пользователи" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-1 flex-col gap-6 p-4">
      <UPageCard
        title="Пользователи"
        description="Здесь назначаются app-роли. Это отдельный слой от роли участника внутри проекта."
      />

      <UCard v-if="props.can_manage">
        <template #header>
          <div class="space-y-1">
            <h2 class="text-lg font-semibold">
              Новый пользователь
            </h2>
            <p class="text-sm text-muted">
              Роль пользователя определяет доступ в сам продукт.
            </p>
          </div>
        </template>

        <UForm
          :state="createForm"
          class="grid gap-4 md:grid-cols-2"
          @submit.prevent="createUser"
        >
          <UFormField
            label="Имя"
            name="name"
            required
            :error="createForm.errors.name"
          >
            <UInput v-model="createForm.name" class="w-full" />
          </UFormField>

          <UFormField
            label="Почта"
            name="email"
            required
            :error="createForm.errors.email"
          >
            <UInput
              v-model="createForm.email"
              class="w-full"
              type="email"
            />
          </UFormField>

          <UFormField
            label="Пароль"
            name="password"
            required
            :error="createForm.errors.password"
          >
            <UInput
              v-model="createForm.password"
              class="w-full"
              type="password"
            />
          </UFormField>

          <UFormField
            label="Подтверждение пароля"
            name="password_confirmation"
            required
            :error="createForm.errors.password_confirmation"
          >
            <UInput
              v-model="createForm.password_confirmation"
              class="w-full"
              type="password"
            />
          </UFormField>

          <UFormField
            label="Статус"
            name="status"
            :error="createForm.errors.status"
          >
            <USelect
              v-model="createForm.status"
              class="w-full"
              :items="[
                { label: 'Активен', value: 'active' },
                { label: 'Неактивен', value: 'inactive' },
                { label: 'Заблокирован', value: 'suspended' },
              ]"
            />
          </UFormField>

          <UFormField
            label="Роль пользователя"
            name="role"
            :error="createForm.errors.role"
          >
            <URadioGroup
              v-model="createForm.role"
              variant="card"
              indicator="end"
              orientation="horizontal"
              :ui="{ item: 'flex-1' }"
              :items="props.role_options.map(role => ({
                label: role.label,
                value: role.value,
              }))"
            />
          </UFormField>

          <div class="md:col-span-2 flex justify-end">
            <UButton
              :loading="createForm.processing"
              icon="i-lucide-user-plus"
              type="submit"
            >
              Создать пользователя
            </UButton>
          </div>
        </UForm>
      </UCard>

      <UCard v-if="props.users.length > 0">
        <template #header>
          <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="space-y-1">
              <h2 class="text-lg font-semibold">
                Доступ в продукт
              </h2>
              <p class="text-sm text-muted">
                Здесь видно, кто вообще может пользоваться Access Atlas и с каким уровнем доступа.
              </p>
            </div>

            <UInput
              v-model="search"
              icon="i-lucide-search"
              placeholder="Поиск по имени или почте"
              class="w-full max-w-xs"
            />
          </div>
        </template>

        <div class="grid gap-4 xl:grid-cols-3">
          <div
            v-for="user in filteredUsers"
            :key="user.id"
            class="space-y-3 rounded-lg border border-default/60 p-4"
          >
            <div class="flex items-start justify-between gap-3">
              <div>
                <p class="font-medium">
                  {{ user.name }}
                </p>
                <p class="text-sm text-muted">
                  {{ user.email }}
                </p>
              </div>
              <p class="text-xs text-muted">
                {{ user.last_login_at ? new Date(user.last_login_at).toLocaleString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'ещё не входил' }}
              </p>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <USelect
                v-model="updateForms[user.id].role"
                class="w-full"
                :items="props.role_options"
                @update:model-value="value => updateUserRole(user.id, String(value), updateForms[user.id].status)"
              />

              <USelect
                v-model="updateForms[user.id].status"
                class="w-full"
                :items="[
                  { label: 'Активен', value: 'active' },
                  { label: 'Неактивен', value: 'inactive' },
                  { label: 'Заблокирован', value: 'suspended' },
                ]"
                @update:model-value="value => updateUserRole(user.id, updateForms[user.id].role, String(value))"
              />
            </div>
          </div>

        </div>

        <p
          v-if="filteredUsers.length === 0 && search"
          class="py-4 text-center text-sm text-muted"
        >
          Ничего не найдено по запросу «{{ search }}»
        </p>
      </UCard>

      <UEmpty
        v-else
        icon="i-lucide-users-round"
        title="Пользователей пока нет"
        description="Создай первого пользователя, чтобы назначить app-role и дать доступ в продукт."
      />
    </div>
  </AppLayout>
</template>
