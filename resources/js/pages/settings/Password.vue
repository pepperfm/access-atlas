<script setup lang="ts">
import type { BreadcrumbItem } from '@/types';
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import PasswordController from '@/actions/App/Http/Controllers/Settings/PasswordController';
import PasswordInput from '@/components/PasswordInput.vue';
import { useFormErrorToast } from '@/composables/useFormErrorToast';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/user-password';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Настройки пароля',
        href: edit(),
    },
];

const page = usePage();
const errors = computed(() => {
    const pageErrors = page.props.errors as Record<string, unknown> | undefined;

    return (
        (pageErrors?.updatePassword as Record<string, string> | undefined) || {}
    );
});

useFormErrorToast(errors, 'Не удалось обновить пароль');
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Настройки пароля" />

        <SettingsLayout>
            <div class="space-y-6">
                <UPageCard
                    title="Обновить пароль"
                    description="Используйте длинный и случайный пароль, чтобы сохранить безопасность аккаунта."
                />

                <UCard>
                    <Form
                        v-slot="{ processing, recentlySuccessful }"
                        v-bind="PasswordController.update.form()"
                        error-bag="updatePassword"
                        :options="{
                            preserveScroll: true,
                        }"
                        reset-on-success
                        :reset-on-error="[
                            'password',
                            'password_confirmation',
                            'current_password',
                        ]"
                        class="space-y-6"
                    >
                        <UFormField
                            label="Текущий пароль"
                            name="current_password"
                            required
                            :error="errors.current_password"
                        >
                            <PasswordInput
                                id="current_password"
                                name="current_password"
                                class="w-full"
                                autocomplete="current-password"
                                placeholder="Текущий пароль"
                            />
                        </UFormField>

                        <UFormField
                            label="Новый пароль"
                            name="password"
                            required
                            :error="errors.password"
                        >
                            <PasswordInput
                                id="password"
                                name="password"
                                class="w-full"
                                autocomplete="new-password"
                                placeholder="Новый пароль"
                            />
                        </UFormField>

                        <UFormField
                            label="Подтвердите пароль"
                            name="password_confirmation"
                            required
                            :error="errors.password_confirmation"
                        >
                            <PasswordInput
                                id="password_confirmation"
                                name="password_confirmation"
                                class="w-full"
                                autocomplete="new-password"
                                placeholder="Подтвердите пароль"
                            />
                        </UFormField>

                        <div class="flex items-center gap-4">
                            <UButton
                                :loading="processing"
                                data-test="update-password-button"
                                type="submit"
                            >
                                Сохранить пароль
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
        </SettingsLayout>
    </AppLayout>
</template>
