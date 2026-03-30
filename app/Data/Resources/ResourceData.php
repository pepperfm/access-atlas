<?php

declare(strict_types=1);

namespace App\Data\Resources;

use Spatie\LaravelData\Data;
use App\Models\Resource;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ResourceData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $provider,
        public readonly string $kind,
        public readonly string $name,
        public readonly ?string $externalIdentifier,
        public readonly string $ownerUserId,
        public readonly string $ownerUserName,
        public readonly string $sensitivity,
        public readonly string $status,
        public readonly ?string $canonicalSource,
        public readonly ?string $notes,
        public readonly ?string $archivedAt,
        #[DataCollectionOf(ProjectResourceData::class)]
        public readonly Collection $projectLinks,
    ) {
    }

    public static function fromModel(Resource $resource): self
    {
        $resource->loadMissing(['ownerUser', 'projectLinks.project', 'projectLinks.environment']);

        return new self(
            id: $resource->id,
            provider: $resource->provider,
            kind: $resource->kind,
            name: $resource->name,
            externalIdentifier: $resource->external_identifier,
            ownerUserId: $resource->owner_user_id,
            ownerUserName: $resource->ownerUser->name,
            sensitivity: $resource->sensitivity->value,
            status: $resource->status->value,
            canonicalSource: $resource->canonical_source,
            notes: $resource->notes,
            archivedAt: $resource->archived_at?->toIso8601String(),
            projectLinks: ProjectResourceData::collect($resource->projectLinks),
        );
    }
}
