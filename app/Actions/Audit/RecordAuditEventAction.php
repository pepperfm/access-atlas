<?php

declare(strict_types=1);

namespace App\Actions\Audit;

use App\Models\AuditEvent;
use App\Models\User;

final readonly class RecordAuditEventAction
{
    /**
     * @param array<string, mixed>|null $meta
     * @param ?User $actor
     * @param string $action
     * @param string $targetType
     * @param string $targetId
     * @param ?string $projectId
     */
    public function handle(
        ?User $actor,
        string $action,
        string $targetType,
        string $targetId,
        ?string $projectId = null,
        ?array $meta = null,
    ): AuditEvent {
        return AuditEvent::query()->create([
            'actor_user_id' => $actor?->id,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'project_id' => $projectId,
            'meta' => $meta,
            'created_at' => now(),
        ]);
    }
}
