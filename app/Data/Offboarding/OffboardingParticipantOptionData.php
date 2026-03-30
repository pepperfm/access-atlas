<?php

declare(strict_types=1);

namespace App\Data\Offboarding;

use Spatie\LaravelData\Data;
use App\Models\User;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class OffboardingParticipantOptionData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $status,
    ) {
    }

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            status: $user->status->value,
        );
    }
}
