<?php

declare(strict_types=1);

namespace App\Data\Search;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class SearchResultData extends Data
{
    public function __construct(
        public readonly string $type,
        public readonly string $id,
        public readonly string $title,
        public readonly string $subtitle,
        public readonly string $href,
    ) {
    }
}
