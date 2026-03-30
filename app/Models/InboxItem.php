<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasUuidPrimaryKey;
use App\Enums\InboxItemStatus;
use App\Enums\InboxSourceType;
use Database\Factories\InboxItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property \App\Enums\InboxSourceType $source_type
 * @property string|null $raw_text
 * @property array<mixed>|null $parsed_summary
 * @property string|null $suggested_project_id
 * @property \App\Enums\InboxItemStatus $status
 * @property string|null $created_by_user_id
 * @property string|null $normalized_by_user_id
 * @property \Carbon\CarbonImmutable|null $normalized_at
 * @property array<mixed>|null $normalized_entities
 * @property \Carbon\CarbonImmutable|null $purged_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 */
class InboxItem extends Model
{
    /** @use HasFactory<InboxItemFactory> */
    use HasFactory;

    use HasUuidPrimaryKey;

    protected function casts(): array
    {
        return [
            'source_type' => InboxSourceType::class,
            'parsed_summary' => 'array',
            'status' => InboxItemStatus::class,
            'normalized_at' => 'immutable_datetime',
            'normalized_entities' => 'array',
            'purged_at' => 'immutable_datetime',
        ];
    }

    public function suggestedProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'suggested_project_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function normalizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'normalized_by_user_id');
    }
}
