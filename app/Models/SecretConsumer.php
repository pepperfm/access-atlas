<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $secret_id
 * @property string|null $user_id
 * @property string|null $resource_id
 * @property string|null $environment_id
 * @property string|null $usage_note
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \App\Models\User|null $user
 */
class SecretConsumer extends Model
{
    use HasUuidPrimaryKey;

    public function secret(): BelongsTo
    {
        return $this->belongsTo(Secret::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function environment(): BelongsTo
    {
        return $this->belongsTo(ProjectEnvironment::class, 'environment_id');
    }
}
