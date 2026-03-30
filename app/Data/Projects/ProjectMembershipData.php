<?php

declare(strict_types=1);

namespace App\Data\Projects;

use Spatie\LaravelData\Data;
use App\Models\ProjectMembership;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ProjectMembershipData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly string $userName,
        public readonly string $projectRole,
        public readonly string $status,
        public readonly ?string $joinedAt,
    ) {
    }

    public static function fromModel(ProjectMembership $membership): self
    {
        $membership->loadMissing('user');

        return new self(
            id: $membership->id,
            userId: $membership->user_id,
            userName: $membership->user->name,
            projectRole: $membership->project_role->label(),
            status: $membership->status->value,
            joinedAt: $membership->joined_at?->toIso8601String(),
        );
    }
}
