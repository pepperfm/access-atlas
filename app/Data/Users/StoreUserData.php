<?php

declare(strict_types=1);

namespace App\Data\Users;

use App\Enums\AppRole;
use App\Enums\UserStatus;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class StoreUserData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $passwordConfirmation,
        public readonly AppRole $role,
        public readonly UserStatus $status,
    ) {
    }

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', Rule::enum(AppRole::class)],
            'status' => ['required', 'string', Rule::enum(UserStatus::class)],
        ];
    }
}
