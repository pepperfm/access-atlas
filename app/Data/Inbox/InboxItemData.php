<?php

declare(strict_types=1);

namespace App\Data\Inbox;

use Spatie\LaravelData\Data;
use App\Models\InboxItem;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class InboxItemData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $sourceType,
        public readonly ?string $rawText,
        public readonly array $parsedSummary,
        public readonly ?string $suggestedProjectId,
        public readonly string $status,
        public readonly ?string $createdByUserId,
        public readonly ?string $normalizedByUserId,
        public readonly ?string $normalizedAt,
        public readonly array $normalizedEntities,
        public readonly ?string $purgedAt,
    ) {
    }

    public static function fromModel(InboxItem $item): self
    {
        return new self(
            id: $item->id,
            sourceType: $item->source_type->value,
            rawText: $item->raw_text,
            parsedSummary: $item->parsed_summary ?? [],
            suggestedProjectId: $item->suggested_project_id,
            status: $item->status->value,
            createdByUserId: $item->created_by_user_id,
            normalizedByUserId: $item->normalized_by_user_id,
            normalizedAt: $item->normalized_at?->toIso8601String(),
            normalizedEntities: $item->normalized_entities ?? [],
            purgedAt: $item->purged_at?->toIso8601String(),
        );
    }
}
