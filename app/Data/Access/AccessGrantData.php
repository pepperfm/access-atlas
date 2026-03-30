<?php

declare(strict_types=1);

namespace App\Data\Access;

use Spatie\LaravelData\Data;
use App\Models\AccessGrant;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class AccessGrantData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $projectId,
        public readonly string $projectName,
        public readonly ?string $environmentId,
        public readonly ?string $environmentName,
        public readonly string $resourceId,
        public readonly string $resourceName,
        public readonly string $userId,
        public readonly string $userName,
        public readonly string $grantKind,
        public readonly string $accessLevel,
        public readonly ?string $scopeDescription,
        public readonly string $ownerUserId,
        public readonly string $ownerUserName,
        public readonly ?string $grantedByUserId,
        public readonly ?string $grantedByUserName,
        public readonly string $status,
        public readonly ?string $startsAt,
        public readonly ?string $expiresAt,
        public readonly ?string $reviewDueAt,
        public readonly ?string $revokedAt,
        public readonly ?string $notes,
    ) {
    }

    public static function fromModel(AccessGrant $grant): self
    {
        $grant->loadMissing(['project', 'environment', 'resource', 'user', 'ownerUser', 'grantedByUser']);

        return new self(
            id: $grant->id,
            projectId: $grant->project_id,
            projectName: $grant->project->name,
            environmentId: $grant->environment_id,
            environmentName: $grant->environment?->name,
            resourceId: $grant->resource_id,
            resourceName: $grant->resource->name,
            userId: $grant->user_id,
            userName: $grant->user->name,
            grantKind: $grant->grant_kind->value,
            accessLevel: $grant->access_level->value,
            scopeDescription: $grant->scope_description,
            ownerUserId: $grant->owner_user_id,
            ownerUserName: $grant->ownerUser->name,
            grantedByUserId: $grant->granted_by_user_id,
            grantedByUserName: $grant->grantedByUser?->name,
            status: $grant->status->value,
            startsAt: $grant->starts_at?->toIso8601String(),
            expiresAt: $grant->expires_at?->toIso8601String(),
            reviewDueAt: $grant->review_due_at?->toIso8601String(),
            revokedAt: $grant->revoked_at?->toIso8601String(),
            notes: $grant->notes,
        );
    }
}
