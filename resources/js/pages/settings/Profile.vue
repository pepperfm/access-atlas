<script setup lang="ts">
import type { BreadcrumbItem } from '@/types';
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/DeleteUser.vue';
import { useFormErrorToast } from '@/composables/useFormErrorToast';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/profile';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Настройки профиля',
        href: edit(),
    },
];

const page = usePage();
const user = computed(() => page.props.auth.user);
const errors = computed(() => {
    const pageErrors = page.props.errors as Record<string, unknown> | undefined;

    return (
        (pageErrors?.updateProfile as Record<string, string> | undefined) || {}
    );
});

useFormErrorToast(errors, 'Не удалось обновить профиль');
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Настройки профиля" />

        <SettingsLayout>
            <div class="space-y-6">
                <UPageCard
                    title="Информация профиля"
                    description="Обновите имя и адрес электронной почты."
                />

                <UCard>
                    <Form
                        v-slot="{ processing, recentlySuccessful }"
                        v-bind="ProfileController.update.form()"
                        error-bag="updateProfile"
                        class="space-y-6"
                    >
                        <UFormField
                            label="Имя"
                            name="name"
                            required
                            :error="errors.name"
                        >
                            <UInput
                                id="name"
                                class="w-full"
                                name="name"
                                :default-value="user.name"
                                autocomplete="name"
                                placeholder="Полное имя"
                            />
                        </UFormField>

                        <UFormField
                            label="Адрес электронной почты"
                            name="email"
                            required
                            :error="errors.email"
                        >
                            <UInput
                                id="email"
                                type="email"
                                class="w-full"
                                name="email"
                                :default-value="user.email"
                                autocomplete="username"
                                placeholder="Адрес электронной почты"
                            />
                        </UFormField>

                        <div class="flex items-center gap-4">
                            <UButton
                                :loading="processing"
                                data-test="update-profile-button"
                                type="submit"
                            >
                                Сохранить
                            </UButton>

                            <p
                                v-show="recentlySuccessful"
                                class="text-sm text-muted"
                            >
                                Сохранено.
                            </p>
                        </div>
                    </Form>
                </UCard>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
