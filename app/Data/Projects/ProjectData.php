<?php

declare(strict_types=1);

namespace App\Data\Projects;

use Spatie\LaravelData\Data;
use App\Models\Project;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ProjectData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $key,
        public readonly string $name,
        public readonly ?string $repositoryUrl,
        public readonly ?string $description,
        public readonly string $status,
        public readonly string $criticality,
        public readonly ?string $archivedAt,
        #[DataCollectionOf(ProjectEnvironmentData::class)]
        public readonly Collection $environments,
        #[DataCollectionOf(ProjectMembershipData::class)]
        public readonly Collection $memberships,
    ) {
    }

    public static function fromModel(Project $project): self
    {
        $project->loadMissing(['environments', 'memberships.user']);

        return new self(
            id: $project->id,
            key: $project->key,
            name: $project->name,
            repositoryUrl: $project->repository_url,
            description: $project->description,
            status: $project->status->value,
            criticality: $project->criticality->value,
            archivedAt: $project->archived_at?->toIso8601String(),
            environments: ProjectEnvironmentData::collect($project->environments),
            memberships: ProjectMembershipData::collect($project->memberships),
        );
    }
}
