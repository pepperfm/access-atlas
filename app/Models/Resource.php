<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasUuidPrimaryKey;
use App\Enums\ResourceStatus;
use App\Enums\SensitivityLevel;
use Database\Factories\ResourceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $provider
 * @property string $kind
 * @property string $name
 * @property string|null $external_identifier
 * @property string $owner_user_id
 * @property \App\Enums\SensitivityLevel $sensitivity
 * @property \App\Enums\ResourceStatus $status
 * @property string|null $canonical_source
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $archived_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \App\Models\User $ownerUser
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProjectResource> $projectLinks
 */
class Resource extends Model
{
    /** @use HasFactory<ResourceFactory> */
    use HasFactory;

    use HasUuidPrimaryKey;

    protected function casts(): array
    {
        return [
            'sensitivity' => SensitivityLevel::class,
            'status' => ResourceStatus::class,
            'archived_at' => 'immutable_datetime',
        ];
    }

    public function ownerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function projectLinks(): HasMany
    {
        return $this->hasMany(ProjectResource::class);
    }

    public function accessGrants(): HasMany
    {
        return $this->hasMany(AccessGrant::class);
    }
}
