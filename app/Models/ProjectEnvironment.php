<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasUuidPrimaryKey;
use App\Enums\EnvironmentStatus;
use Database\Factories\ProjectEnvironmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $project_id
 * @property string $key
 * @property string $name
 * @property bool $is_production
 * @property \App\Enums\EnvironmentStatus $status
 * @property int $sort_order
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \App\Models\Project $project
 */
class ProjectEnvironment extends Model
{
    /** @use HasFactory<ProjectEnvironmentFactory> */
    use HasFactory;

    use HasUuidPrimaryKey;

    protected function casts(): array
    {
        return [
            'is_production' => 'bool',
            'status' => EnvironmentStatus::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
