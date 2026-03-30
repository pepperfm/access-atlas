<?php

declare(strict_types=1);

namespace App\Actions\Reviews;

use App\Data\Reviews\CompleteReviewTaskData;
use App\Enums\ReviewTaskStatus;
use App\Models\ReviewTask;

final readonly class CompleteReviewTaskAction
{
    public function handle(ReviewTask $task, CompleteReviewTaskData $data): ReviewTask
    {
        $task->update([
            'status' => ReviewTaskStatus::Completed,
            'result' => $data->result,
            'comment' => $data->comment,
            'completed_at' => now(),
        ]);

        return $task->refresh();
    }
}
