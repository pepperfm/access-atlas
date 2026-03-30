<?php

declare(strict_types=1);

namespace App\Data\Projects;

use Spatie\LaravelData\Data;
use App\Enums\ProjectCriticality;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class StoreProjectData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $repositoryUrl,
        public readonly ?string $description,
        public readonly ProjectCriticality $criticality,
    ) {
    }

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'repositoryUrl' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
            'criticality' => ['required', 'string', Rule::enum(ProjectCriticality::class)],
        ];
    }
}
