<?php

declare(strict_types=1);

namespace App\Data\Offboarding;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class OffboardingPageData extends Data
{
    public function __construct(
        #[DataCollectionOf(OffboardingParticipantOptionData::class)]
        public readonly Collection $users,
        public readonly ?OffboardingSummaryData $summary,
    ) {
    }
}
