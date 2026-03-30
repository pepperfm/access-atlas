<?php

declare(strict_types=1);

namespace App\Data\Resources;

use Spatie\LaravelData\Data;
use App\Models\ProjectResource;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ProjectResourceData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $projectId,
        public readonly string $projectName,
        public readonly ?string $environmentId,
        public readonly ?string $environmentName,
        public readonly string $relationType,
        public readonly string $status,
    ) {
    }

    public static function fromModel(ProjectResource $projectResource): self
    {
        $projectResource->loadMissing(['project', 'environment']);

        return new self(
            id: $projectResource->id,
            projectId: $projectResource->project_id,
            projectName: $projectResource->project->name,
            environmentId: $projectResource->environment_id,
            environmentName: $projectResource->environment?->name,
            relationType: $projectResource->relation_type,
            status: $projectResource->status->value,
        );
    }
}
