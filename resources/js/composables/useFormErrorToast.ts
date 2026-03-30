import type { MaybeRefOrGetter } from 'vue'
import { ref, toValue, watch } from 'vue'

type FormErrors = Record<string, string | string[] | null | undefined> | null | undefined

export function useFormErrorToast(
  errors: MaybeRefOrGetter<FormErrors>,
  title: string,
): void {
  const toast = useToast()
  const lastDigest = ref<string>('')

  watch(
    () => toValue(errors),
    (value) => {
      if (!value || Object.keys(value).length === 0) {
        lastDigest.value = ''
        return
      }

      const messages = Object.values(value)
        .flatMap(message => Array.isArray(message) ? message : [message])
        .filter((message): message is string => Boolean(message))

      if (messages.length === 0) {
        lastDigest.value = ''
        return
      }

      const digest = JSON.stringify(messages)

      if (digest === lastDigest.value) {
        return
      }

      lastDigest.value = digest

      messages.forEach((message) => {
        toast.add({
          color: 'error',
          title,
          description: message,
        })
      })
    },
    { deep: true, immediate: true },
  )
}
