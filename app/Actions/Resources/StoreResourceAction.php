<?php

declare(strict_types=1);

namespace App\Actions\Resources;

use App\Actions\Audit\RecordAuditEventAction;
use App\Data\Resources\StoreResourceData;
use App\Enums\ResourceStatus;
use App\Models\Resource;
use App\Models\User;

final readonly class StoreResourceAction
{
    public function __construct(
        private RecordAuditEventAction $recordAuditEvent,
    ) {
    }

    public function handle(User $actor, StoreResourceData $data): Resource
    {
        $resource = Resource::query()->create([
            'provider' => $data->provider,
            'kind' => $data->kind,
            'name' => $data->name,
            'external_identifier' => $data->externalIdentifier,
            'owner_user_id' => $data->ownerUserId,
            'sensitivity' => $data->sensitivity,
            'status' => ResourceStatus::Active,
            'canonical_source' => $data->canonicalSource,
            'notes' => $data->notes,
        ]);

        $this->recordAuditEvent->handle(
            actor: $actor,
            action: 'resource.created',
            targetType: Resource::class,
            targetId: $resource->id,
            meta: [
                'provider' => $resource->provider,
                'kind' => $resource->kind,
            ],
        );

        return $resource->load('ownerUser');
    }
}
