<?php

declare(strict_types=1);

namespace App\Data\Inbox;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class NormalizeInboxItemData extends Data
{
    public function __construct(
        public readonly array $parsedSummary,
        public readonly ?string $projectId,
        public readonly ?string $environmentId,
        public readonly ?string $name,
        public readonly ?string $secretType,
        public readonly ?string $storageMode,
        public readonly ?string $externalReference,
        public readonly ?string $encryptedValue,
        public readonly ?string $ownerUserId,
        public readonly ?string $revealPolicy,
        public readonly ?string $sensitivity,
    ) {
    }

    public static function rules(): array
    {
        return [
            'parsedSummary' => ['required', 'array'],
            'projectId' => ['nullable', 'uuid', 'exists:projects,id'],
            'environmentId' => ['nullable', 'uuid', 'exists:project_environments,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'secretType' => ['nullable', 'string', 'max:255'],
            'storageMode' => ['nullable', 'string'],
            'externalReference' => ['nullable', 'string'],
            'encryptedValue' => ['nullable', 'string'],
            'ownerUserId' => ['nullable', 'uuid', 'exists:users,id'],
            'revealPolicy' => ['nullable', 'string'],
            'sensitivity' => ['nullable', 'string'],
        ];
    }
}
