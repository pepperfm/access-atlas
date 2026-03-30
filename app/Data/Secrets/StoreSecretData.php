<?php

declare(strict_types=1);

namespace App\Data\Secrets;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use App\Enums\SecretRevealPolicy;
use App\Enums\SecretStatus;
use App\Enums\SecretStorageMode;
use App\Enums\SensitivityLevel;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

#[MapName(SnakeCaseMapper::class)]
final class StoreSecretData extends Data
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $environmentId,
        public readonly ?string $resourceId,
        public readonly string $name,
        public readonly string $secretType,
        public readonly SensitivityLevel $sensitivity,
        public readonly SecretStorageMode $storageMode,
        public readonly ?string $externalReference,
        public readonly ?string $encryptedValue,
        public readonly ?string $ownerUserId,
        public readonly SecretStatus $status,
        public readonly SecretRevealPolicy $revealPolicy,
        public readonly ?string $rotationDueAt,
        public readonly ?string $notes,
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        return [
            'project_id' => ['required', 'uuid', 'exists:projects,id'],
            'environment_id' => ['required', 'uuid', 'exists:project_environments,id'],
            'resource_id' => ['nullable', 'uuid', 'exists:resources,id'],
            'name' => ['required', 'string', 'max:255'],
            'secret_type' => ['required', 'string', 'max:255'],
            'sensitivity' => ['required', 'string', Rule::enum(SensitivityLevel::class)],
            'storage_mode' => ['required', 'string', Rule::enum(SecretStorageMode::class)],
            'external_reference' => [
                'nullable',
                'string',
                Rule::requiredIf(
                    static fn(): bool => Arr::toString($context->fullPayload, 'storage_mode') === SecretStorageMode::ExternalReference->value,
                ),
            ],
            'encrypted_value' => [
                'nullable',
                'string',
                Rule::requiredIf(
                    static fn(): bool => Arr::toString($context->fullPayload, 'storage_mode') === SecretStorageMode::EncryptedValue->value,
                ),
            ],
            'owner_user_id' => ['nullable', 'uuid', 'exists:users,id'],
            'status' => ['required', 'string', Rule::enum(SecretStatus::class)],
            'reveal_policy' => ['required', 'string', Rule::enum(SecretRevealPolicy::class)],
            'rotation_due_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
