<?php

declare(strict_types=1);

namespace App\Actions\Access;

use App\Actions\Audit\RecordAuditEventAction;
use App\Data\Access\StoreAccessGrantData;
use App\Enums\AccessGrantStatus;
use App\Models\AccessGrant;
use App\Models\User;

final readonly class StoreAccessGrantAction
{
    public function __construct(
        private RecordAuditEventAction $recordAuditEvent,
    ) {
    }

    public function handle(User $actor, StoreAccessGrantData $data): AccessGrant
    {
        $grant = AccessGrant::query()->create([
            'project_id' => $data->projectId,
            'environment_id' => $data->environmentId,
            'resource_id' => $data->resourceId,
            'user_id' => $data->userId,
            'grant_kind' => $data->grantKind,
            'access_level' => $data->accessLevel,
            'scope_description' => $data->scopeDescription,
            'owner_user_id' => $data->ownerUserId,
            'granted_by_user_id' => $actor->id,
            'status' => AccessGrantStatus::Active,
            'starts_at' => $data->startsAt,
            'expires_at' => $data->expiresAt,
            'review_due_at' => $data->reviewDueAt,
            'notes' => $data->notes,
        ]);

        $this->recordAuditEvent->handle(
            actor: $actor,
            action: 'access_grant.created',
            targetType: AccessGrant::class,
            targetId: $grant->id,
            projectId: $grant->project_id,
            meta: [
                'resource_id' => $grant->resource_id,
                'user_id' => $grant->user_id,
                'access_level' => $grant->access_level->value,
            ],
        );

        return $grant->load(['project', 'environment', 'resource', 'user', 'ownerUser', 'grantedByUser']);
    }
}
