<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string|null $actor_user_id
 * @property string $action
 * @property string $target_type
 * @property string $target_id
 * @property string|null $project_id
 * @property array<string, mixed>|null $meta
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \App\Models\User|null $actorUser
 * @property \App\Models\Project|null $project
 */
class AuditEvent extends Model
{
    use HasUuidPrimaryKey;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'created_at' => 'immutable_datetime',
        ];
    }

    public function actorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
