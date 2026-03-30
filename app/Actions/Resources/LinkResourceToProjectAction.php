<?php

declare(strict_types=1);

namespace App\Actions\Resources;

use App\Actions\Audit\RecordAuditEventAction;
use App\Data\Resources\LinkProjectResourceData;
use App\Models\ProjectResource;
use App\Models\User;

final readonly class LinkResourceToProjectAction
{
    public function __construct(
        private RecordAuditEventAction $recordAuditEvent,
    ) {
    }

    public function handle(User $actor, LinkProjectResourceData $data): ProjectResource
    {
        $projectResource = ProjectResource::query()->updateOrCreate(
            [
                'project_id' => $data->projectId,
                'resource_id' => $data->resourceId,
                'environment_id' => $data->environmentId,
            ],
            [
                'relation_type' => $data->relationType,
                'status' => $data->status,
            ],
        );

        $this->recordAuditEvent->handle(
            actor: $actor,
            action: 'project_resource.linked',
            targetType: ProjectResource::class,
            targetId: $projectResource->id,
            projectId: $projectResource->project_id,
            meta: [
                'resource_id' => $projectResource->resource_id,
                'environment_id' => $projectResource->environment_id,
                'relation_type' => $projectResource->relation_type,
            ],
        );

        return $projectResource->load(['project', 'resource', 'environment']);
    }
}
