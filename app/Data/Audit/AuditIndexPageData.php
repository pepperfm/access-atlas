<?php

declare(strict_types=1);

namespace App\Data\Audit;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class AuditIndexPageData extends Data
{
    public function __construct(
        #[DataCollectionOf(AuditEventData::class)]
        public readonly Collection $events,
    ) {
    }
}
