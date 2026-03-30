<?php

declare(strict_types=1);

namespace App\Data\Users;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class UsersIndexPageData extends Data
{
    public function __construct(
        public readonly bool $canManage,
        #[DataCollectionOf(UserData::class)]
        public readonly Collection $users,
        public readonly array $roleOptions,
    ) {
    }
}
