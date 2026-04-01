<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-svh flex-col items-center justify-center gap-6 bg-muted p-6 md:p-10"
    >
        <div class="flex w-full max-w-md flex-col gap-6">
            <ULink
                raw
                :to="home.url()"
                class="flex items-center gap-2 self-center font-medium"
            >
                <div class="flex h-9 w-9 items-center justify-center">
                    <AppLogoIcon class="size-9 text-black dark:text-white" />
                </div>
            </ULink>

            <div class="flex flex-col gap-6">
                <UCard
                    variant="subtle"
                    :ui="{
                        root: 'rounded-2xl',
                        header: 'px-10 pt-8 pb-0 text-center',
                        body: 'px-10 py-8',
                    }"
                >
                    <template #header>
                        <h1 class="text-xl font-semibold">
                            {{ title }}
                        </h1>
                        <p class="mt-1 text-sm text-muted">
                            {{ description }}
                        </p>
                    </template>

                    <div>
                        <slot />
                    </div>
                </UCard>
            </div>
        </div>
    </div>
</template>
