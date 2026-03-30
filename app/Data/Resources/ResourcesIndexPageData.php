<?php

declare(strict_types=1);

namespace App\Data\Resources;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ResourcesIndexPageData extends Data
{
    public function __construct(
        public readonly bool $canManage,
        public readonly array $userOptions,
        #[DataCollectionOf(ResourceData::class)]
        public readonly Collection $resources,
    ) {
    }
}
