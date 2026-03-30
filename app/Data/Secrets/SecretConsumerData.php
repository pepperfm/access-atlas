<?php

declare(strict_types=1);

namespace App\Data\Secrets;

use Spatie\LaravelData\Data;
use App\Models\SecretConsumer;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class SecretConsumerData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $userId,
        public readonly ?string $userName,
        public readonly ?string $resourceId,
        public readonly ?string $environmentId,
        public readonly ?string $usageNote,
    ) {
    }

    public static function fromModel(SecretConsumer $consumer): self
    {
        $consumer->loadMissing('user');

        return new self(
            id: $consumer->id,
            userId: $consumer->user_id,
            userName: $consumer->user?->name,
            resourceId: $consumer->resource_id,
            environmentId: $consumer->environment_id,
            usageNote: $consumer->usage_note,
        );
    }
}
