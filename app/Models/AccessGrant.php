<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasUuidPrimaryKey;
use App\Enums\AccessGrantKind;
use App\Enums\AccessGrantStatus;
use App\Enums\AccessLevel;
use Database\Factories\AccessGrantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $project_id
 * @property string|null $environment_id
 * @property string $resource_id
 * @property string $user_id
 * @property \App\Enums\AccessGrantKind $grant_kind
 * @property \App\Enums\AccessLevel $access_level
 * @property string|null $scope_description
 * @property string $owner_user_id
 * @property string|null $granted_by_user_id
 * @property \App\Enums\AccessGrantStatus $status
 * @property \Carbon\CarbonImmutable|null $starts_at
 * @property \Carbon\CarbonImmutable|null $expires_at
 * @property \Carbon\CarbonImmutable|null $review_due_at
 * @property \Carbon\CarbonImmutable|null $revoked_at
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \App\Models\Project $project
 * @property \App\Models\ProjectEnvironment|null $environment
 * @property \App\Models\Resource $resource
 * @property \App\Models\User $user
 * @property \App\Models\User $ownerUser
 * @property \App\Models\User|null $grantedByUser
 */
class AccessGrant extends Model
{
    /** @use HasFactory<AccessGrantFactory> */
    use HasFactory;

    use HasUuidPrimaryKey;

    protected function casts(): array
    {
        return [
            'grant_kind' => AccessGrantKind::class,
            'access_level' => AccessLevel::class,
            'status' => AccessGrantStatus::class,
            'starts_at' => 'immutable_datetime',
            'expires_at' => 'immutable_datetime',
            'review_due_at' => 'immutable_datetime',
            'revoked_at' => 'immutable_datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function environment(): BelongsTo
    {
        return $this->belongsTo(ProjectEnvironment::class, 'environment_id');
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ownerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function grantedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by_user_id');
    }
}
