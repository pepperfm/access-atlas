<?php

declare(strict_types=1);

namespace App\Data\Access;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use App\Enums\AccessGrantKind;
use App\Enums\AccessLevel;
use Illuminate\Validation\Rule;

#[MapName(SnakeCaseMapper::class)]
final class StoreAccessGrantData extends Data
{
    public function __construct(
        public readonly string $projectId,
        public readonly ?string $environmentId,
        public readonly string $resourceId,
        public readonly string $userId,
        public readonly AccessGrantKind $grantKind,
        public readonly AccessLevel $accessLevel,
        public readonly ?string $scopeDescription,
        public readonly string $ownerUserId,
        public readonly ?\Carbon\CarbonImmutable $startsAt,
        public readonly ?\Carbon\CarbonImmutable $expiresAt,
        public readonly ?\Carbon\CarbonImmutable $reviewDueAt,
        public readonly ?string $notes,
    ) {
    }

    public static function rules(): array
    {
        return [
            'projectId' => ['required', 'uuid', 'exists:projects,id'],
            'environmentId' => ['nullable', 'uuid', 'exists:project_environments,id'],
            'resourceId' => ['required', 'uuid', 'exists:resources,id'],
            'userId' => ['required', 'uuid', 'exists:users,id'],
            'grantKind' => ['required', 'string', Rule::enum(AccessGrantKind::class)],
            'accessLevel' => ['required', 'string', Rule::enum(AccessLevel::class)],
            'scopeDescription' => ['nullable', 'string'],
            'ownerUserId' => ['required', 'uuid', 'exists:users,id'],
            'startsAt' => ['nullable', 'date'],
            'expiresAt' => ['nullable', 'date'],
            'reviewDueAt' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
