<?php

declare(strict_types=1);

namespace App\Data\Inbox;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class InboxIndexPageData extends Data
{
    public function __construct(
        public readonly bool $canManage,
        public readonly array $projectOptions,
        public readonly array $environmentOptions,
        public readonly array $userOptions,
        #[DataCollectionOf(InboxItemData::class)]
        public readonly Collection $items,
    ) {
    }
}
