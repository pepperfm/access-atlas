<script setup lang="ts">
import { Form, usePage } from '@inertiajs/vue3';
import { computed, ref, useTemplateRef } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import PasswordInput from '@/components/PasswordInput.vue';
import { useFormErrorToast } from '@/composables/useFormErrorToast';

const passwordInput = useTemplateRef('passwordInput');
const open = ref(false);
const page = usePage();
const errors = computed(() => {
    const pageErrors = page.props.errors as Record<string, unknown> | undefined;

    return (pageErrors?.deleteUser as Record<string, string> | undefined) || {};
});

useFormErrorToast(errors, 'Не удалось удалить аккаунт');
</script>

<template>
    <div class="space-y-6">
        <UPageCard
            title="Удалить аккаунт"
            description="Это действие безвозвратно удалит аккаунт и связанные с ним данные."
        />

        <UAlert
            color="error"
            variant="soft"
            icon="i-lucide-triangle-alert"
            title="Внимание"
            description="После удаления аккаунта откатить действие уже не получится."
        />

        <UModal
            v-model:open="open"
            title="Удалить аккаунт?"
            description="Введите пароль, чтобы подтвердить удаление аккаунта."
        >
            <UButton color="error" data-test="delete-user-button">
                Удалить аккаунт
            </UButton>

            <template #body>
                <Form
                    v-slot="{ processing, reset, clearErrors }"
                    v-bind="ProfileController.destroy.form()"
                    error-bag="deleteUser"
                    reset-on-success
                    :options="{
                        preserveScroll: true,
                    }"
                    class="space-y-6"
                    @error="() => passwordInput?.focus()"
                >
                    <UFormField
                        label="Пароль"
                        name="password"
                        required
                        :error="errors.password"
                    >
                        <PasswordInput
                            id="password"
                            ref="passwordInput"
                            name="password"
                            class="w-full"
                            placeholder="Пароль"
                        />
                    </UFormField>

                    <div class="flex items-center justify-end gap-3">
                        <UButton
                            color="neutral"
                            variant="subtle"
                            @click="
                                () => {
                                    clearErrors();
                                    reset();
                                    open = false;
                                }
                            "
                        >
                            Отмена
                        </UButton>

                        <UButton
                            type="submit"
                            color="error"
                            :loading="processing"
                            data-test="confirm-delete-user-button"
                        >
                            Удалить аккаунт
                        </UButton>
                    </div>
                </Form>
            </template>
        </UModal>
    </div>
</template>
