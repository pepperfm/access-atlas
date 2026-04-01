<script setup lang="ts">
import type { NavigationMenuItem } from '@nuxt/ui';
import type { NavItem } from '@/types';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { edit as editPassword } from '@/routes/user-password';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Профиль',
        href: editProfile(),
    },
    {
        title: 'Пароль',
        href: editPassword(),
    },
    {
        title: 'Внешний вид',
        href: editAppearance(),
    },
];

const { isCurrentOrParentUrl } = useCurrentUrl();

const navigationItems = computed<NavigationMenuItem[][]>(() => [
    [
        {
            label: 'Разделы',
            type: 'label',
            value: 'settings-label',
        },
        ...sidebarNavItems.map((item) => ({
            label: item.title,
            to: item.href,
            active: isCurrentOrParentUrl(item.href),
            value: item.title,
        })),
    ],
]);
</script>

<template>
    <div class="px-4 py-6">
        <Heading
            title="Настройки"
            description="Управляйте профилем и параметрами аккаунта"
        />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <UNavigationMenu
                    orientation="vertical"
                    :items="navigationItems"
                    color="neutral"
                    class="w-full"
                    :ui="{
                        root: 'w-full',
                        list: 'w-full',
                        label: 'px-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-muted',
                        link: 'rounded-lg px-3 py-2 text-sm font-medium data-[active=true]:bg-accented data-[active=true]:text-default hover:bg-muted',
                    }"
                />
            </aside>

            <USeparator class="my-6 lg:hidden" />

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
