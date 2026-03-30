<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\User;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    /**
     * Get the validation rules used to validate user profiles.
     *
     * @param ?string $userId
     *
     * @return array<string, array<int, mixed>>
     */
    protected function profileRules(?string $userId = null): array
    {
        return [
            'name' => $this->nameRules(),
            'email' => $this->emailRules($userId),
        ];
    }

    /**
     * Get the validation rules used to validate user names.
     *
     * @return array<int, mixed>
     */
    protected function nameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * Get the validation rules used to validate user emails.
     *
     * @param ?string $userId
     *
     * @return array<int, mixed>
     */
    protected function emailRules(?string $userId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            $userId === null
                ? Rule::unique(User::class)
                : Rule::unique(User::class)->ignore($userId),
        ];
    }
}
