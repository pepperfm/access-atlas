<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\AppPermission;
use App\Data\Offboarding\OffboardingChecklistItemData;
use App\Data\Offboarding\OffboardingPageData;
use App\Data\Offboarding\OffboardingParticipantOptionData;
use App\Data\Offboarding\OffboardingSummaryData;
use App\Models\AccessGrant;
use App\Models\Resource;
use App\Models\ReviewTask;
use App\Models\Secret;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Response;

final readonly class OffboardingController
{
    public function index(Request $request): Response
    {
        abort_unless(user()->can(AppPermission::OffboardingView->value), 403);

        $users = User::query()->orderBy('name')->get();
        $selectedUser = $request->string('user_id')->isNotEmpty()
            ? User::query()->find($request->string('user_id')->value())
            : null;

        return inertia('Offboarding/Index', new OffboardingPageData(
            users: OffboardingParticipantOptionData::collect($users),
            summary: $selectedUser instanceof User
                ? $this->buildSummary($selectedUser)
                : null,
        )->toArray());
    }

    private function buildSummary(User $user): OffboardingSummaryData
    {
        $user->loadMissing(['projectMemberships.project']);

        $accessGrants = AccessGrant::query()
            ->with(['resource', 'project'])
            ->where('user_id', $user->id)
            ->where('status', '!=', 'revoked')
            ->get();

        $ownedResources = Resource::query()
            ->where('owner_user_id', $user->id)
            ->get();

        $ownedSecrets = Secret::query()
            ->with('project')
            ->where('owner_user_id', $user->id)
            ->get();

        $assignedReviews = ReviewTask::query()
            ->where('assigned_to_user_id', $user->id)
            ->where('status', '!=', 'completed')
            ->get();

        return new OffboardingSummaryData(
            userId: $user->id,
            userName: $user->name,
            status: $user->status->value,
            memberships: $user->projectMemberships->map(
                fn($membership): OffboardingChecklistItemData => new OffboardingChecklistItemData(
                    title: $membership->project->name,
                    description: implode(' · ', [$membership->project_role->label(), $membership->status->value]),
                    href: route('projects.show', $membership->project),
                ),
            ),
            accessGrants: $accessGrants->map(
                fn(AccessGrant $grant): OffboardingChecklistItemData => new OffboardingChecklistItemData(
                    title: $grant->resource->name,
                    description: implode(' · ', [$grant->project->name, $grant->access_level->value, $grant->status->value]),
                    href: route('access-grants.index'),
                ),
            ),
            ownedResources: $ownedResources->map(
                fn(Resource $resource): OffboardingChecklistItemData => new OffboardingChecklistItemData(
                    title: $resource->name,
                    description: implode(' · ', [$resource->provider, $resource->status->value]),
                    href: route('resources.show', $resource),
                ),
            ),
            ownedSecrets: $ownedSecrets->map(
                fn(Secret $secret): OffboardingChecklistItemData => new OffboardingChecklistItemData(
                    title: $secret->name,
                    description: implode(' · ', [$secret->project->name, $secret->status->value]),
                    href: route('secrets.index'),
                ),
            ),
            assignedReviews: $assignedReviews->map(
                fn(ReviewTask $task): OffboardingChecklistItemData => new OffboardingChecklistItemData(
                    title: $task->task_type->value,
                    description: implode(' · ', [$task->target_type->value, $task->status->value]),
                    href: route('reviews.index'),
                ),
            ),
        );
    }
}
