<script setup lang="ts">
import type { User } from '@/types';
import { computed } from 'vue';
import { useInitials } from '@/composables/useInitials';

interface Props {
    user: User;
    showEmail?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
});

const { getInitials } = useInitials();

// Compute whether we should show the avatar image
const showAvatar = computed(
    () => props.user.avatar && props.user.avatar !== '',
);
</script>

<template>
    <div class="flex min-w-0 items-center gap-3">
        <UAvatar
            :src="showAvatar ? user.avatar : undefined"
            :alt="user.name"
            :text="getInitials(user.name)"
            size="md"
        />

        <div class="grid min-w-0 flex-1 text-left text-sm leading-tight">
            <span class="truncate font-medium">{{ user.name }}</span>
            <span v-if="showEmail" class="truncate text-xs text-muted">
                {{ user.email }}
            </span>
        </div>
    </div>
</template>
