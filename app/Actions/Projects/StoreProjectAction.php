<?php

declare(strict_types=1);

namespace App\Actions\Projects;

use App\Data\Projects\StoreProjectData;
use App\Enums\EnvironmentStatus;
use App\Enums\MembershipStatus;
use App\Enums\ProjectRole;
use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectEnvironment;
use App\Models\ProjectMembership;
use App\Models\User;

final readonly class StoreProjectAction
{
    public function handle(User $actor, StoreProjectData $data): Project
    {
        return db()->transaction(function () use ($actor, $data): Project {
            $projectKey = $this->generateProjectKey($data->name);

            $project = Project::query()->create([
                'key' => $projectKey,
                'name' => str($data->name)->trim()->value(),
                'repository_url' => blank($data->repositoryUrl) ? null : str($data->repositoryUrl)->trim()->value(),
                'description' => str($data->description ?? '')->trim()->value() ?: null,
                'status' => ProjectStatus::Active,
                'criticality' => $data->criticality,
                'created_by_user_id' => $actor->id,
            ]);

            collect([
                ['key' => 'dev', 'name' => 'Development', 'is_production' => false, 'sort_order' => 10],
                ['key' => 'stage', 'name' => 'Stage', 'is_production' => false, 'sort_order' => 20],
                ['key' => 'prod', 'name' => 'Production', 'is_production' => true, 'sort_order' => 30],
            ])->each(function (array $environment) use ($project): void {
                ProjectEnvironment::query()->create([
                    'project_id' => $project->id,
                    'key' => $environment['key'],
                    'name' => $environment['name'],
                    'is_production' => $environment['is_production'],
                    'status' => EnvironmentStatus::Active,
                    'sort_order' => $environment['sort_order'],
                ]);
            });

            ProjectMembership::query()->create([
                'project_id' => $project->id,
                'user_id' => $actor->id,
                'project_role' => ProjectRole::Owner,
                'status' => MembershipStatus::Active,
                'joined_at' => now(),
            ]);

            if (!$project->ownerMemberships()->exists()) {
                logger()->error('[projects.store] Owner membership was not created.', [
                    'project_id' => $project->id,
                    'actor_user_id' => $actor->id,
                ]);

                throw new \RuntimeException('Project must have at least one owner membership.');
            }

            return $project->load(['environments', 'memberships.user']);
        });
    }

    private function generateProjectKey(string $name): string
    {
        $baseKey = str($name)->trim()->lower()->slug()->value();
        $baseKey = blank($baseKey) ? 'project' : $baseKey;
        $candidate = $baseKey;
        $suffix = 2;

        while (Project::query()->where('key', $candidate)->exists()) {
            $candidate = "{$baseKey}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }
}
