<?php

declare(strict_types=1);

namespace App\Data\Offboarding;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class OffboardingSummaryData extends Data
{
    public function __construct(
        public readonly string $userId,
        public readonly string $userName,
        public readonly string $status,
        #[DataCollectionOf(OffboardingChecklistItemData::class)]
        public readonly Collection $memberships,
        #[DataCollectionOf(OffboardingChecklistItemData::class)]
        public readonly Collection $accessGrants,
        #[DataCollectionOf(OffboardingChecklistItemData::class)]
        public readonly Collection $ownedResources,
        #[DataCollectionOf(OffboardingChecklistItemData::class)]
        public readonly Collection $ownedSecrets,
        #[DataCollectionOf(OffboardingChecklistItemData::class)]
        public readonly Collection $assignedReviews,
    ) {
    }
}
