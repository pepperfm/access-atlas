<?php

declare(strict_types=1);

namespace App\Data\Secrets;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class SecretsIndexPageData extends Data
{
    public function __construct(
        public readonly bool $canManage,
        public readonly bool $canReveal,
        public readonly ?string $defaultOwnerUserId,
        public readonly array $projectOptions,
        public readonly array $environmentOptions,
        public readonly array $resourceOptions,
        public readonly array $userOptions,
        #[DataCollectionOf(SecretData::class)]
        public readonly Collection $secrets,
    ) {
    }
}
