<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ResourceStatus;
use App\Enums\SensitivityLevel;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<resource>
 */
class ResourceFactory extends Factory
{
    protected $model = Resource::class;

    public function definition(): array
    {
        return [
            'provider' => fake()->randomElement(['github', 'aws', 'stripe']),
            'kind' => fake()->randomElement(['repo', 'account', 'bucket']),
            'name' => fake()->words(2, true),
            'external_identifier' => fake()->uuid(),
            'owner_user_id' => null,
            'sensitivity' => SensitivityLevel::Medium,
            'status' => ResourceStatus::Active,
            'canonical_source' => fake()->url(),
            'notes' => fake()->sentence(),
            'archived_at' => null,
        ];
    }
}
