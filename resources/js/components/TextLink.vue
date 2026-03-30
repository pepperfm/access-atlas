<script setup lang="ts">
import type { InertiaLinkProps } from '@inertiajs/vue3'

interface Props {
  href: NonNullable<InertiaLinkProps['href']>
  tabindex?: number
  as?: string
}

defineProps<Props>()
</script>

<template>
  <ULink
    raw
    :to="typeof href === 'string' ? href : href.url"
    :tabindex="tabindex"
    :as="as"
    class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
  >
    <slot />
  </ULink>
</template>
