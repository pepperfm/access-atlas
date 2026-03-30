<?php

declare(strict_types=1);

namespace App\Data\Auth;

use Spatie\LaravelData\Data;
use App\Models\User;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class AuthenticatedUserData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $status,
        public readonly ?string $lastLoginAt,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
    ) {
    }

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            status: $user->status->value,
            lastLoginAt: $user->last_login_at?->toIso8601String(),
            createdAt: $user->created_at?->toIso8601String(),
            updatedAt: $user->updated_at?->toIso8601String(),
        );
    }
}
