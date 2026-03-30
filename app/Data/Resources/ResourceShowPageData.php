<?php

declare(strict_types=1);

namespace App\Data\Resources;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ResourceShowPageData extends Data
{
    public function __construct(
        public readonly bool $canManage,
        public readonly array $projectOptions,
        public readonly array $environmentOptions,
        public readonly ResourceData $resource,
    ) {
    }
}
