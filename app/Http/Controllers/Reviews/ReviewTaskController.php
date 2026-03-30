<?php

declare(strict_types=1);

namespace App\Http\Controllers\Reviews;

use App\Actions\Reviews\CompleteReviewTaskAction;
use App\Actions\Reviews\StoreReviewTaskAction;
use App\Data\Reviews\CompleteReviewTaskData;
use App\Data\Reviews\ReviewsIndexPageData;
use App\Data\Reviews\ReviewTaskData;
use App\Data\Reviews\StoreReviewTaskData;
use App\Models\AccessGrant;
use App\Models\Resource;
use App\Models\ReviewTask;
use App\Models\Secret;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

final readonly class ReviewTaskController
{
    public function index(): Response
    {
        $tasks = ReviewTask::query()->latest()->get();

        return inertia(
            'Reviews/Index',
            new ReviewsIndexPageData(
                canManage: auth()->check(),
                assigneeOptions: User::query()->orderBy('name')->get(['id', 'name', 'email'])
                    ->map(fn(User $user): array => [
                        'value' => $user->id,
                        'label' => "$user->name ($user->email)",
                    ])->all(),
                targetOptions: [
                    'secret' => Secret::query()->orderBy('name')->get(['id', 'name'])
                        ->map(fn(Secret $secret): array => [
                            'value' => $secret->id,
                            'label' => $secret->name,
                        ])->all(),
                    'access_grant' => AccessGrant::query()->with(['user', 'resource'])->latest()->get()
                        ->map(fn(AccessGrant $grant): array => [
                            'value' => $grant->id,
                            'label' => $grant->user->name . ' -> ' . $grant->resource->name,
                        ])->all(),
                    'resource' => Resource::query()->orderBy('name')->get(['id', 'name', 'provider'])
                        ->map(fn(Resource $resource): array => [
                            'value' => $resource->id,
                            'label' => "$resource->name ($resource->provider)",
                        ])->all(),
                ],
                tasks: ReviewTaskData::collect($tasks),
            )->toArray(),
        );
    }

    public function store(StoreReviewTaskData $data, StoreReviewTaskAction $storeReviewTaskAction): RedirectResponse
    {
        $storeReviewTaskAction->handle($data);

        return to_route('reviews.index');
    }

    public function complete(
        CompleteReviewTaskData $data,
        ReviewTask $reviewTask,
        CompleteReviewTaskAction $completeReviewTaskAction,
    ): RedirectResponse {
        $completeReviewTaskAction->handle($reviewTask, $data);

        return to_route('reviews.index');
    }
}
