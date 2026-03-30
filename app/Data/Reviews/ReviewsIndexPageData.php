<?php

declare(strict_types=1);

namespace App\Data\Reviews;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ReviewsIndexPageData extends Data
{
    public function __construct(
        public readonly bool $canManage,
        public readonly array $assigneeOptions,
        public readonly array $targetOptions,
        #[DataCollectionOf(ReviewTaskData::class)]
        public readonly Collection $tasks,
    ) {
    }
}
