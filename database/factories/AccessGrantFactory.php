<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AccessGrantKind;
use App\Enums\AccessGrantStatus;
use App\Enums\AccessLevel;
use App\Models\AccessGrant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccessGrant>
 */
class AccessGrantFactory extends Factory
{
    protected $model = AccessGrant::class;

    public function definition(): array
    {
        return [
            'project_id' => null,
            'environment_id' => null,
            'resource_id' => null,
            'user_id' => null,
            'grant_kind' => AccessGrantKind::Credential,
            'access_level' => AccessLevel::Read,
            'scope_description' => fake()->sentence(),
            'owner_user_id' => null,
            'granted_by_user_id' => null,
            'status' => AccessGrantStatus::Active,
            'starts_at' => now(),
            'expires_at' => now()->addWeek(),
            'review_due_at' => now()->addDays(30),
            'revoked_at' => null,
            'notes' => fake()->sentence(),
        ];
    }
}
