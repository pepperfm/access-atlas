<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasUuidPrimaryKey;
use App\Enums\SecretRevealPolicy;
use App\Enums\SecretStatus;
use App\Enums\SecretStorageMode;
use App\Enums\SensitivityLevel;
use Database\Factories\SecretFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $project_id
 * @property string $environment_id
 * @property string|null $resource_id
 * @property string $name
 * @property string $secret_type
 * @property \App\Enums\SensitivityLevel $sensitivity
 * @property \App\Enums\SecretStorageMode $storage_mode
 * @property string|null $external_reference
 * @property string|null $encrypted_value
 * @property string|null $owner_user_id
 * @property \App\Enums\SecretStatus $status
 * @property \App\Enums\SecretRevealPolicy $reveal_policy
 * @property \Carbon\CarbonImmutable|null $last_verified_at
 * @property \Carbon\CarbonImmutable|null $last_rotated_at
 * @property \Carbon\CarbonImmutable|null $rotation_due_at
 * @property string|null $notes
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \App\Models\Project $project
 * @property \App\Models\ProjectEnvironment $environment
 * @property \App\Models\User|null $ownerUser
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\SecretConsumer> $consumers
 */
class Secret extends Model
{
    /** @use HasFactory<SecretFactory> */
    use HasFactory;

    use HasUuidPrimaryKey;

    protected $hidden = [
        'encrypted_value',
    ];

    protected function casts(): array
    {
        return [
            'sensitivity' => SensitivityLevel::class,
            'storage_mode' => SecretStorageMode::class,
            'encrypted_value' => 'encrypted',
            'status' => SecretStatus::class,
            'reveal_policy' => SecretRevealPolicy::class,
            'last_verified_at' => 'immutable_datetime',
            'last_rotated_at' => 'immutable_datetime',
            'rotation_due_at' => 'immutable_datetime',
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

    public function ownerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function consumers(): HasMany
    {
        return $this->hasMany(SecretConsumer::class);
    }
}
