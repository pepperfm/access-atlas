<?php

declare(strict_types=1);

namespace App\Data\Projects;

use App\Enums\ProjectRole;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class AssignProjectParticipantData extends Data
{
    public function __construct(
        public readonly string $userId,
        public readonly ProjectRole $projectRole,
    ) {
    }

    public static function rules(): array
    {
        return [
            'userId' => ['required', 'uuid', 'exists:users,id'],
            'projectRole' => ['required', 'string', Rule::enum(ProjectRole::class)],
        ];
    }
}
