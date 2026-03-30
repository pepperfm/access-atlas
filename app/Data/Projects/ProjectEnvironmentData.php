<?php

declare(strict_types=1);

namespace App\Data\Projects;

use Spatie\LaravelData\Data;
use App\Models\ProjectEnvironment;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ProjectEnvironmentData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $key,
        public readonly string $name,
        public readonly bool $isProduction,
        public readonly string $status,
        public readonly int $sortOrder,
    ) {
    }

    public static function fromModel(ProjectEnvironment $environment): self
    {
        return new self(
            id: $environment->id,
            key: $environment->key,
            name: $environment->name,
            isProduction: $environment->is_production,
            status: $environment->status->value,
            sortOrder: $environment->sort_order,
        );
    }
}
