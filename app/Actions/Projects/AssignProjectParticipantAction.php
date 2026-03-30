<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Data\Projects\AssignProjectParticipantData;
use App\Enums\MembershipStatus;
use App\Models\Project;
use App\Models\ProjectMembership;

final readonly class AssignProjectParticipantAction
{
    public function handle(Project $project, AssignProjectParticipantData $data): ProjectMembership
    {
        $membership = ProjectMembership::query()->updateOrCreate(
            [
                'project_id' => $project->id,
                'user_id' => $data->userId,
            ],
            [
                'project_role' => $data->projectRole,
                'status' => MembershipStatus::Active,
                'joined_at' => now(),
                'left_at' => null,
            ],
        );

        return $membership->loadMissing('user');
    }
}
