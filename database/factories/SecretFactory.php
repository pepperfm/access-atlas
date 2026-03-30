<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SecretRevealPolicy;
use App\Enums\SecretStatus;
use App\Enums\SecretStorageMode;
use App\Enums\SensitivityLevel;
use App\Models\Secret;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Secret>
 */
class SecretFactory extends Factory
{
    protected $model = Secret::class;

    public function definition(): array
    {
        return [
            'project_id' => null,
            'environment_id' => null,
            'resource_id' => null,
            'name' => fake()->slug(),
            'secret_type' => fake()->randomElement(['api_key', 'db_password']),
            'sensitivity' => SensitivityLevel::Medium,
            'storage_mode' => SecretStorageMode::ExternalReference,
            'external_reference' => fake()->url(),
            'encrypted_value' => null,
            'owner_user_id' => null,
            'status' => SecretStatus::Active,
            'reveal_policy' => SecretRevealPolicy::ProjectOwnerOrSecurityAdmin,
            'last_verified_at' => null,
            'last_rotated_at' => null,
            'rotation_due_at' => now()->addMonth(),
            'notes' => fake()->sentence(),
        ];
    }
}
