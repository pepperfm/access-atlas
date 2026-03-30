<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasUuidPrimaryKey;
use App\Enums\ResourceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $project_id
 * @property string $resource_id
 * @property string|null $environment_id
 * @property string $relation_type
 * @property \App\Enums\ResourceStatus $status
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \App\Models\Project $project
 * @property \App\Models\Resource $resource
 * @property \App\Models\ProjectEnvironment|null $environment
 */
class ProjectResource extends Model
{
    use HasUuidPrimaryKey;

    protected function casts(): array
    {
        return [
            'status' => ResourceStatus::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function environment(): BelongsTo
    {
        return $this->belongsTo(ProjectEnvironment::class, 'environment_id');
    }
}
