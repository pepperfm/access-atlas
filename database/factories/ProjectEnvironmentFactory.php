<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EnvironmentStatus;
use App\Models\ProjectEnvironment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectEnvironment>
 */
class ProjectEnvironmentFactory extends Factory
{
    protected $model = ProjectEnvironment::class;

    public function definition(): array
    {
        return [
            'project_id' => null,
            'key' => fake()->unique()->randomElement(['dev', 'stage', 'prod']),
            'name' => fake()->randomElement(['Development', 'Stage', 'Production']),
            'is_production' => false,
            'status' => EnvironmentStatus::Active,
            'sort_order' => fake()->numberBetween(10, 50),
        ];
    }
}
