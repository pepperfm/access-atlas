<?php

declare(strict_types=1);

namespace App\Data\Reviews;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use App\Enums\ReviewTaskTargetType;
use App\Enums\ReviewTaskType;
use Illuminate\Validation\Rule;

#[MapName(SnakeCaseMapper::class)]
final class StoreReviewTaskData extends Data
{
    public function __construct(
        public readonly ReviewTaskTargetType $targetType,
        public readonly string $targetId,
        public readonly ReviewTaskType $taskType,
        public readonly ?string $assignedToUserId,
        public readonly string $dueAt,
        public readonly ?string $comment,
    ) {
    }

    public static function rules(): array
    {
        return [
            'targetType' => ['required', 'string', Rule::enum(ReviewTaskTargetType::class)],
            'targetId' => ['required', 'uuid'],
            'taskType' => ['required', 'string', Rule::enum(ReviewTaskType::class)],
            'assignedToUserId' => ['nullable', 'uuid', 'exists:users,id'],
            'dueAt' => ['required', 'date'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
