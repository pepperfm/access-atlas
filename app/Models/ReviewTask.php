<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasUuidPrimaryKey;
use App\Enums\ReviewTaskStatus;
use App\Enums\ReviewTaskTargetType;
use App\Enums\ReviewTaskType;
use Database\Factories\ReviewTaskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property \App\Enums\ReviewTaskTargetType $target_type
 * @property string $target_id
 * @property \App\Enums\ReviewTaskType $task_type
 * @property string|null $assigned_to_user_id
 * @property \Carbon\CarbonImmutable $due_at
 * @property \App\Enums\ReviewTaskStatus $status
 * @property string|null $result
 * @property string|null $comment
 * @property \Carbon\CarbonImmutable|null $completed_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 */
class ReviewTask extends Model
{
    /** @use HasFactory<ReviewTaskFactory> */
    use HasFactory;

    use HasUuidPrimaryKey;

    protected function casts(): array
    {
        return [
            'target_type' => ReviewTaskTargetType::class,
            'task_type' => ReviewTaskType::class,
            'due_at' => 'immutable_datetime',
            'status' => ReviewTaskStatus::class,
            'completed_at' => 'immutable_datetime',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
}
