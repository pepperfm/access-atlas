<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasUuidPrimaryKey;
use App\Enums\MembershipStatus;
use App\Enums\ProjectRole;
use Database\Factories\ProjectMembershipFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $project_id
 * @property string $user_id
 * @property \App\Enums\ProjectRole $project_role
 * @property \App\Enums\MembershipStatus $status
 * @property \Carbon\CarbonImmutable|null $joined_at
 * @property \Carbon\CarbonImmutable|null $left_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \App\Models\Project $project
 * @property \App\Models\User $user
 */
class ProjectMembership extends Model
{
    /** @use HasFactory<ProjectMembershipFactory> */
    use HasFactory;

    use HasUuidPrimaryKey;

    protected function casts(): array
    {
        return [
            'project_role' => ProjectRole::class,
            'status' => MembershipStatus::class,
            'joined_at' => 'immutable_datetime',
            'left_at' => 'immutable_datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
