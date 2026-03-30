<?php

declare(strict_types=1);

namespace App\Data\Projects;

use App\Models\User;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ProjectParticipantOptionData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
    ) {
    }

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
        );
    }
}
