<?php

declare(strict_types=1);

namespace App\Data\Resources;

use Spatie\LaravelData\Data;
use App\Enums\ResourceStatus;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class LinkProjectResourceData extends Data
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $resourceId,
        public readonly ?string $environmentId,
        public readonly string $relationType,
        public readonly ResourceStatus $status,
    ) {
    }

    public static function rules(): array
    {
        return [
            'projectId' => ['required', 'uuid'],
            'resourceId' => ['required', 'uuid'],
            'environmentId' => ['nullable', 'uuid'],
            'relationType' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', Rule::enum(ResourceStatus::class)],
        ];
    }
}
