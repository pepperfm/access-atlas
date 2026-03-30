<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProjectCriticality;
use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'name' => fake()->company(),
            'repository_url' => fake()->optional()->url(),
            'description' => fake()->sentence(),
            'status' => ProjectStatus::Active,
            'criticality' => ProjectCriticality::Medium,
            'created_by_user_id' => null,
            'archived_at' => null,
        ];
    }
}
