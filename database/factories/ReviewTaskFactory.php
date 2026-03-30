<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ReviewTaskStatus;
use App\Enums\ReviewTaskTargetType;
use App\Enums\ReviewTaskType;
use App\Models\ReviewTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReviewTask>
 */
class ReviewTaskFactory extends Factory
{
    protected $model = ReviewTask::class;

    public function definition(): array
    {
        return [
            'target_type' => ReviewTaskTargetType::AccessGrant,
            'target_id' => str()->uuid()->toString(),
            'task_type' => ReviewTaskType::AccessReview,
            'assigned_to_user_id' => null,
            'due_at' => now()->addWeek(),
            'status' => ReviewTaskStatus::Open,
            'result' => null,
            'comment' => fake()->sentence(),
            'completed_at' => null,
        ];
    }
}
