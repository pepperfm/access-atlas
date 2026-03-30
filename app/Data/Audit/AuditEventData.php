<?php

declare(strict_types=1);

namespace App\Data\Audit;

use Spatie\LaravelData\Data;
use App\Models\AuditEvent;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class AuditEventData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $action,
        public readonly string $targetType,
        public readonly string $targetId,
        public readonly ?string $projectName,
        public readonly ?string $actorName,
        public readonly ?string $createdAt,
    ) {
    }

    public static function fromModel(AuditEvent $event): self
    {
        $event->loadMissing(['actorUser', 'project']);

        return new self(
            id: $event->id,
            action: $event->action,
            targetType: $event->target_type,
            targetId: $event->target_id,
            projectName: $event->project?->name,
            actorName: $event->actorUser?->name,
            createdAt: $event->created_at?->toIso8601String(),
        );
    }
}
