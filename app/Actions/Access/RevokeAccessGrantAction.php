<?php

declare(strict_types=1);

namespace App\Actions\Access;

use App\Actions\Audit\RecordAuditEventAction;
use App\Enums\AccessGrantStatus;
use App\Models\AccessGrant;
use App\Models\User;

final readonly class RevokeAccessGrantAction
{
    public function __construct(
        private RecordAuditEventAction $recordAuditEvent,
    ) {
    }

    public function handle(User $actor, AccessGrant $grant): AccessGrant
    {
        $grant->update([
            'status' => AccessGrantStatus::Revoked,
            'revoked_at' => now(),
        ]);

        $this->recordAuditEvent->handle(
            actor: $actor,
            action: 'access_grant.revoked',
            targetType: AccessGrant::class,
            targetId: $grant->id,
            projectId: $grant->project_id,
            meta: [
                'resource_id' => $grant->resource_id,
                'user_id' => $grant->user_id,
            ],
        );

        return $grant->refresh()->load(['project', 'environment', 'resource', 'user', 'ownerUser', 'grantedByUser']);
    }
}
