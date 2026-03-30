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
final class UpdateUserRoleData extends Data
{
    public function __construct(
        public readonly AppRole $role,
        public readonly UserStatus $status,
    ) {
    }

    public static function rules(): array
    {
        return [
            'role' => ['required', 'string', Rule::enum(AppRole::class)],
            'status' => ['required', 'string', Rule::enum(UserStatus::class)],
        ];
    }
}
