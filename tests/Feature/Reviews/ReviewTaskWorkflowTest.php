<?php

declare(strict_types=1);

use App\Actions\Reviews\CompleteReviewTaskAction;
use App\Actions\Reviews\StoreReviewTaskAction;
use App\Data\Reviews\CompleteReviewTaskData;
use App\Data\Reviews\StoreReviewTaskData;
use App\Enums\ReviewTaskStatus;
use App\Enums\ReviewTaskTargetType;
use App\Enums\ReviewTaskType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('review tasks can be created and completed', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');

    $task = app(StoreReviewTaskAction::class)->handle(StoreReviewTaskData::from([
        'target_type' => ReviewTaskTargetType::Secret,
        'target_id' => str()->uuid()->toString(),
        'task_type' => ReviewTaskType::SecretVerification,
        'assigned_to_user_id' => $actor->id,
        'due_at' => now()->addDay()->toIso8601String(),
        'comment' => 'Verify secret ownership.',
    ]));

    $completed = app(CompleteReviewTaskAction::class)->handle($task, CompleteReviewTaskData::from([
        'result' => 'confirmed',
        'comment' => 'Still in use.',
    ]));

    expect($completed->status)->toBe(ReviewTaskStatus::Completed)
        ->and($completed->result)->toBe('confirmed')
        ->and($completed->completed_at)->not()->toBeNull();
});
