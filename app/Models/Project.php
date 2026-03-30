<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasUuidPrimaryKey;
use App\Enums\ProjectCriticality;
use App\Enums\ProjectRole;
use App\Enums\ProjectStatus;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $key
 * @property string $name
 * @property string|null $repository_url
 * @property string|null $description
 * @property \App\Enums\ProjectStatus $status
 * @property \App\Enums\ProjectCriticality $criticality
 * @property string|null $created_by_user_id
 * @property \Carbon\CarbonImmutable|null $archived_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 */
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    use HasUuidPrimaryKey;

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'criticality' => ProjectCriticality::class,
            'archived_at' => 'immutable_datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function environments(): HasMany
    {
        return $this->hasMany(ProjectEnvironment::class)->orderBy('sort_order');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(ProjectMembership::class);
    }

    public function ownerMemberships(): HasMany
    {
        return $this->memberships()
            ->where('project_role', ProjectRole::Owner->value);
    }
}
