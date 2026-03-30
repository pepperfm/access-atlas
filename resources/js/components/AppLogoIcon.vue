<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { KeySquare } from 'lucide-vue-next'

defineOptions({
  inheritAttrs: false,
})

defineProps<Props>()

interface Props {
  className?: HTMLAttributes['class']
}
</script>

<template>
  <KeySquare :class="className" v-bind="$attrs" />
</template>
