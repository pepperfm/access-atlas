<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { Eye, EyeOff } from 'lucide-vue-next';
import { ref, useTemplateRef } from 'vue';

defineOptions({ inheritAttrs: false });

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();

const showPassword = ref(false);
const input = useTemplateRef<{ inputRef?: HTMLInputElement | null }>('input');

defineExpose({
    focus: () => input.value?.inputRef?.focus?.(),
});
</script>

<template>
    <UInput
        ref="input"
        v-bind="$attrs"
        :type="showPassword ? 'text' : 'password'"
        :class="props.class"
    >
        <template #trailing>
            <UButton
                color="neutral"
                variant="ghost"
                size="xs"
                :icon="showPassword ? EyeOff : Eye"
                :aria-label="showPassword ? 'Скрыть пароль' : 'Показать пароль'"
                @click="showPassword = !showPassword"
            />
        </template>
    </UInput>
</template>
