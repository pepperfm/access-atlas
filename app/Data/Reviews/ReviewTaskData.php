<?php

declare(strict_types=1);

namespace App\Data\Reviews;

use Spatie\LaravelData\Data;
use App\Models\ReviewTask;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ReviewTaskData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $targetType,
        public readonly string $targetId,
        public readonly string $taskType,
        public readonly ?string $assignedToUserId,
        public readonly string $dueAt,
        public readonly string $status,
        public readonly ?string $result,
        public readonly ?string $comment,
        public readonly ?string $completedAt,
    ) {
    }

    public static function fromModel(ReviewTask $task): self
    {
        return new self(
            id: $task->id,
            targetType: $task->target_type->value,
            targetId: $task->target_id,
            taskType: $task->task_type->value,
            assignedToUserId: $task->assigned_to_user_id,
            dueAt: $task->due_at->toIso8601String(),
            status: $task->status->value,
            result: $task->result,
            comment: $task->comment,
            completedAt: $task->completed_at?->toIso8601String(),
        );
    }
}
