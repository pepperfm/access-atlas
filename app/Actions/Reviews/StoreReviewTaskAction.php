<?php

declare(strict_types=1);

namespace App\Actions\Reviews;

use App\Data\Reviews\StoreReviewTaskData;
use App\Models\ReviewTask;

final readonly class StoreReviewTaskAction
{
    public function handle(StoreReviewTaskData $data): ReviewTask
    {
        return ReviewTask::query()->create([
            'target_type' => $data->targetType,
            'target_id' => $data->targetId,
            'task_type' => $data->taskType,
            'assigned_to_user_id' => $data->assignedToUserId,
            'due_at' => $data->dueAt,
            'comment' => $data->comment,
        ]);
    }
}
