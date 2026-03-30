<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Enums\MembershipStatus;
use App\Models\Project;
use App\Models\ProjectMembership;

final readonly class RemoveProjectParticipantAction
{
    public function handle(Project $project, string $userId): void
    {
        ProjectMembership::query()
            ->where('project_id', $project->id)
            ->where('user_id', $userId)
            ->update([
                'status' => MembershipStatus::Left,
                'left_at' => now(),
            ]);
    }
}
