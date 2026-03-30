<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Enums\ProjectStatus;
use App\Models\Project;

final readonly class ArchiveProjectAction
{
    public function handle(Project $project): Project
    {
        if ($project->status === ProjectStatus::Archived) {
            return $project;
        }

        $project->update([
            'status' => ProjectStatus::Archived,
            'archived_at' => now(),
        ]);

        return $project->refresh()->load(['environments', 'memberships.user']);
    }
}
