<script setup lang="ts">
import type { AuthFormField } from '@nuxt/ui'
import type { LoginPageProps } from '@/types'
import { Head, router, usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import { useFormErrorToast } from '@/composables/useFormErrorToast'
import { store } from '@/routes/login'

const props = defineProps<LoginPageProps>()

interface LoginFormState {
  email: string
  password: string
}

interface FormError {
  name?: string
  message: string
}

interface AuthFormExpose {
  formRef: {
    clear: () => void
    setErrors: (errors: FormError[]) => void
  } | null
}

const page = usePage()
const authForm = ref<AuthFormExpose | null>(null)
const isSubmitting = ref(false)

useFormErrorToast(
  computed(() => page.props.errors as Record<string, string> | undefined),
  'Не удалось войти',
)

const fields = computed<AuthFormField[]>(() => [
  {
    name: 'email',
    type: 'email',
    label: 'Почта',
    placeholder: 'email@example.com',
    autocomplete: 'email',
    required: true,
    autofocus: true,
  },
  {
    name: 'password',
    type: 'password',
    label: 'Пароль',
    placeholder: 'Пароль',
    autocomplete: 'current-password',
    required: true,
  },
])

watch(
  () => page.props.errors as Record<string, string> | undefined,
  (errors) => {
    authForm.value?.formRef?.clear()

    if (!errors) {
      return
    }

    authForm.value?.formRef?.setErrors(
      Object.entries(errors).map(([name, message]) => ({
        name,
        message,
      })),
    )
  },
  { immediate: true },
)

function submit(event: SubmitEvent & { data: LoginFormState }): void {
  isSubmitting.value = true

  router.post(store.url(), { ...event.data }, {
    preserveScroll: true,
    onFinish: () => {
      isSubmitting.value = false
    },
  })
}
</script>

<template>
  <Head title="Вход" />

  <UApp :toaster="{ duration: 2500 }">
    <div
      class="flex min-h-screen items-center justify-center bg-[radial-gradient(circle_at_top,_color-mix(in_oklab,var(--ui-primary)_10%,transparent),transparent_32%),linear-gradient(180deg,var(--ui-bg)_0%,var(--ui-bg-muted)_100%)] px-6 py-12"
    >
      <div class="w-full max-w-md">
        <UAuthForm
          ref="authForm"
          class="rounded-[2rem] border border-default/70 bg-default/90 px-8 py-10 shadow-2xl shadow-primary/10 backdrop-blur sm:px-10"
          :fields="fields"
          title="Access Atlas"
          description="Войдите, чтобы открыть реестр доступов и секретов."
          icon="i-lucide-lock-keyhole"
          :submit="{
            label: 'Войти',
            block: true,
            loading: isSubmitting,
          }"
          :loading="isSubmitting"
          @submit="submit"
        >
          <template #validation>
            <UAlert
              v-if="props.status"
              color="success"
              variant="soft"
              :description="props.status"
            />
          </template>

          <template #footer>
            <p class="text-center text-sm text-muted">
              Внутренний project-first реестр доступов и секретов.
            </p>
          </template>
        </UAuthForm>
      </div>
    </div>
  </UApp>
</template>
