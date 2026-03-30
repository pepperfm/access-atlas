<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Data\Projects\UpdateProjectData;
use App\Models\Project;

final readonly class UpdateProjectAction
{
    public function handle(Project $project, UpdateProjectData $data): Project
    {
        $project->update([
            'name' => str($data->name)->trim()->value(),
            'repository_url' => blank($data->repositoryUrl) ? null : str($data->repositoryUrl)->trim()->value(),
            'description' => blank($data->description) ? null : str($data->description)->trim()->value(),
            'criticality' => $data->criticality,
        ]);

        return $project->refresh()->load(['environments', 'memberships.user']);
    }
}
