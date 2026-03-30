<?php

declare(strict_types=1);

namespace App\Data\Secrets;

use Spatie\LaravelData\Data;
use App\Models\Secret;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class SecretData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $projectId,
        public readonly string $projectName,
        public readonly string $environmentId,
        public readonly string $environmentName,
        public readonly ?string $resourceId,
        public readonly string $name,
        public readonly string $secretType,
        public readonly string $sensitivity,
        public readonly string $storageMode,
        public readonly ?string $externalReference,
        public readonly bool $hasEncryptedValue,
        public readonly ?string $ownerUserId,
        public readonly ?string $ownerUserName,
        public readonly string $status,
        public readonly string $revealPolicy,
        public readonly ?string $lastVerifiedAt,
        public readonly ?string $lastRotatedAt,
        public readonly ?string $rotationDueAt,
        public readonly ?string $notes,
        #[DataCollectionOf(SecretConsumerData::class)]
        public readonly Collection $consumers,
    ) {
    }

    public static function fromModel(Secret $secret): self
    {
        $secret->loadMissing(['project', 'environment', 'ownerUser', 'consumers.user']);

        return new self(
            id: $secret->id,
            projectId: $secret->project_id,
            projectName: $secret->project->name,
            environmentId: $secret->environment_id,
            environmentName: $secret->environment->name,
            resourceId: $secret->resource_id,
            name: $secret->name,
            secretType: $secret->secret_type,
            sensitivity: $secret->sensitivity->value,
            storageMode: $secret->storage_mode->value,
            externalReference: $secret->external_reference,
            hasEncryptedValue: filled($secret->getRawOriginal('encrypted_value')),
            ownerUserId: $secret->owner_user_id,
            ownerUserName: $secret->ownerUser?->name,
            status: $secret->status->value,
            revealPolicy: $secret->reveal_policy->value,
            lastVerifiedAt: $secret->last_verified_at?->toIso8601String(),
            lastRotatedAt: $secret->last_rotated_at?->toIso8601String(),
            rotationDueAt: $secret->rotation_due_at?->toIso8601String(),
            notes: $secret->notes,
            consumers: SecretConsumerData::collect($secret->consumers),
        );
    }
}
