<script setup lang="ts">
import type { DropdownMenuItem, NavigationMenuItem } from '@nuxt/ui';
import type { BreadcrumbItem, User } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { useColorMode } from '@vueuse/core';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
import AppGlobalSearch from '@/components/AppGlobalSearch.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { getInitials } from '@/composables/useInitials';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

interface SidebarNavItem {
    label: string;
    href: string;
    icon: string;
}

const page = usePage();
const sidebarCollapsed = ref(false);
const sidebarOpen = ref(false);
const userMenuOpen = ref(false);

const currentUser = computed(() => page.props.auth.user as User);
const appName = computed(() => page.props.name ?? 'Access Atlas');
const colorMode = useColorMode();
const { isCurrentOrParentUrl } = useCurrentUrl();

const mainNavigation: SidebarNavItem[] = [
    {
        label: 'Дашборд',
        href: route('dashboard'),
        icon: 'i-lucide-layout-grid',
    },
    {
        label: 'Проекты',
        href: route('projects.index'),
        icon: 'i-lucide-folder-open-dot',
    },
    {
        label: 'Пользователи',
        href: route('users.index'),
        icon: 'i-lucide-users-round',
    },
    {
        label: 'Ресурсы',
        href: route('resources.index'),
        icon: 'i-lucide-server-cog',
    },
    {
        label: 'Доступы',
        href: route('access-grants.index'),
        icon: 'i-lucide-key-round',
    },
    {
        label: 'Секреты',
        href: route('secrets.index'),
        icon: 'i-lucide-shield-ellipsis',
    },
    {
        label: 'Ревью',
        href: route('reviews.index'),
        icon: 'i-lucide-scroll-text',
    },
    { label: 'Инбокс', href: route('inbox.index'), icon: 'i-lucide-inbox' },
];

const secondaryNavigation: SidebarNavItem[] = [
    {
        label: 'Аудит',
        href: route('audit.index'),
        icon: 'i-lucide-shield-alert',
    },
    {
        label: 'Оффбординг',
        href: route('offboarding.index'),
        icon: 'i-lucide-folder-git-2',
    },
];

function toNavigationGroup(
    id: string,
    label: string,
    items: SidebarNavItem[],
): NavigationMenuItem[] {
    return [
        {
            label,
            type: 'label',
            value: `${id}-label`,
        },
        ...items.map((item) => ({
            label: item.label,
            icon: item.icon,
            to: item.href,
            active: isCurrentOrParentUrl(item.href),
            onSelect: (event: Event) => {
                event.preventDefault();
                router.visit(item.href);
                sidebarOpen.value = false;
            },
            value: `${id}-${item.label}`,
        })),
    ];
}

const navigationItems = computed<NavigationMenuItem[][]>(() => [
    toNavigationGroup('main', 'Реестр', mainNavigation),
    toNavigationGroup('ops', 'Операции', secondaryNavigation),
]);

const userLabel = computed(
    () => currentUser.value.name || currentUser.value.email || 'Owner',
);
const userAvatar = computed(() => ({
    src: currentUser.value.avatar || undefined,
    alt: userLabel.value,
    text: getInitials(userLabel.value),
}));

function closeUserMenu(): void {
    userMenuOpen.value = false;
}

function withClose(action: () => void): () => void {
    return () => {
        action();
        closeUserMenu();
    };
}

const userMenuItems = computed<DropdownMenuItem[][]>(() => [
    [
        {
            type: 'label',
            label: userLabel.value,
            avatar: userAvatar.value,
        },
    ],
    [
        {
            label: 'Тема',
            type: 'label',
        },
        {
            label: 'Светлая',
            icon: 'i-lucide-sun',
            type: 'checkbox',
            checked: colorMode.value === 'light',
            onSelect: withClose(() => {
                colorMode.store.value = 'light';
            }),
            onUpdateChecked: () => {
                colorMode.store.value = 'light';
            },
        },
        {
            label: 'Тёмная',
            icon: 'i-lucide-moon',
            type: 'checkbox',
            checked: colorMode.value === 'dark',
            onSelect: withClose(() => {
                colorMode.store.value = 'dark';
            }),
            onUpdateChecked: () => {
                colorMode.store.value = 'dark';
            },
        },
    ],
    [
        {
            label: 'Настройки',
            icon: 'i-lucide-settings',
            onSelect: withClose(() => router.visit(route('profile.edit'))),
        },
        {
            label: 'Выйти',
            icon: 'i-lucide-log-out',
            color: 'error',
            onSelect: withClose(() => {
                router.flushAll();
                router.post(route('logout'));
            }),
        },
    ],
]);
</script>

<template>
    <UApp :toaster="{ duration: 2500 }">
        <UDashboardGroup
            storage="cookie"
            storage-key="access-atlas-dashboard"
            unit="rem"
            class="min-h-screen bg-default"
        >
            <UDashboardSidebar
                id="app"
                v-model:collapsed="sidebarCollapsed"
                v-model:open="sidebarOpen"
                collapsible
                :default-size="17"
                :min-size="17"
                :max-size="20"
                :collapsed-size="5"
                class="bg-elevated/30 max-sm:!w-[18rem] sm:data-[collapsed=false]:!w-[17rem] sm:data-[collapsed=true]:!w-[5rem]"
            >
                <template #header="{ collapsed }">
                    <div class="flex items-center px-3 py-4">
                        <UButton
                            color="neutral"
                            variant="ghost"
                            class="w-full justify-start px-2"
                            @click="router.visit(route('dashboard'))"
                        >
                            <template #leading>
                                <div
                                    class="flex size-8 items-center justify-center rounded-lg bg-inverted text-inverted"
                                >
                                    <AppLogoIcon class="size-5" />
                                </div>
                            </template>

                            <span
                                v-if="!collapsed"
                                class="truncate text-sm font-semibold"
                            >
                                {{ appName }}
                            </span>
                        </UButton>
                    </div>
                </template>

                <template #default="{ collapsed }">
                    <div class="flex h-full flex-col">
                        <div class="flex-1 overflow-y-auto px-2 pb-4">
                            <UNavigationMenu
                                orientation="vertical"
                                :collapsed="collapsed"
                                :tooltip="collapsed"
                                :items="navigationItems"
                                highlight
                                color="neutral"
                                class="py-2"
                                :ui="{
                                    root: 'flex flex-col gap-4',
                                    list: 'flex flex-col gap-4',
                                    label: 'px-2 text-[11px] font-semibold uppercase tracking-[0.24em] text-muted',
                                    separator: 'my-2',
                                    link: 'rounded-lg px-3 py-2 text-sm font-medium data-[active=true]:bg-accented data-[active=true]:text-default hover:bg-muted',
                                }"
                            />
                        </div>
                    </div>
                </template>

                <template #footer="{ collapsed }">
                    <div class="flex w-full flex-col gap-3 px-3 py-3">
                        <UDropdownMenu
                            v-model:open="userMenuOpen"
                            :items="userMenuItems"
                            :content="{ align: 'center', collisionPadding: 12 }"
                            :ui="{
                                content: collapsed
                                    ? 'w-48'
                                    : 'w-(--reka-dropdown-menu-trigger-width)',
                            }"
                        >
                            <UButton
                                :avatar="userAvatar"
                                color="neutral"
                                variant="ghost"
                                class="w-full data-[state=open]:bg-elevated"
                                :block="!collapsed"
                                :square="collapsed"
                                :label="collapsed ? undefined : userLabel"
                                :trailing-icon="
                                    collapsed
                                        ? undefined
                                        : 'i-lucide-chevrons-up-down'
                                "
                                :ui="{ trailingIcon: 'text-muted' }"
                            />
                        </UDropdownMenu>
                    </div>
                </template>
            </UDashboardSidebar>

            <UDashboardPanel class="min-w-0 bg-default">
                <template #header>
                    <UDashboardNavbar
                        :ui="{
                            root: 'border-b border-default px-4 sm:px-6',
                            left: 'min-w-0 gap-3',
                            right: 'gap-3',
                        }"
                    >
                        <template #left>
                            <div class="flex min-w-0 items-center gap-2">
                                <UDashboardSidebarCollapse
                                    class="hidden lg:inline-flex"
                                />
                                <Breadcrumbs
                                    v-if="props.breadcrumbs.length > 0"
                                    :breadcrumbs="props.breadcrumbs"
                                />
                            </div>
                        </template>

                        <template #right>
                            <AppGlobalSearch />
                        </template>
                    </UDashboardNavbar>
                </template>

                <template #body>
                    <div class="min-w-0 overflow-x-hidden">
                        <slot />
                    </div>
                </template>
            </UDashboardPanel>
        </UDashboardGroup>
    </UApp>
</template>
