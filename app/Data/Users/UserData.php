<?php

declare(strict_types=1);

namespace App\Data\Users;

use App\Models\User;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class UserData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $status,
        public readonly ?string $role,
        public readonly ?string $lastLoginAt,
    ) {
    }

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            status: $user->status->value,
            role: $user->roles->first()?->name,
            lastLoginAt: $user->last_login_at?->toIso8601String(),
        );
    }
}
