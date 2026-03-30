<?php

declare(strict_types=1);

namespace App\Data\Resources;

use Spatie\LaravelData\Data;
use App\Enums\SensitivityLevel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class StoreResourceData extends Data
{
    public function __construct(
        public readonly string $provider,
        public readonly string $kind,
        public readonly string $name,
        public readonly ?string $externalIdentifier,
        public readonly string $ownerUserId,
        public readonly SensitivityLevel $sensitivity,
        public readonly ?string $canonicalSource,
        public readonly ?string $notes,
    ) {
    }

    public static function rules(): array
    {
        return [
            'provider' => ['required', 'string', 'max:100'],
            'kind' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'externalIdentifier' => ['nullable', 'string', 'max:255'],
            'ownerUserId' => ['required', 'uuid', 'exists:users,id'],
            'sensitivity' => ['required', 'string', Rule::enum(SensitivityLevel::class)],
            'canonicalSource' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
