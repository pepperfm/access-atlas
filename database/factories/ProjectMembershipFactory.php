<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MembershipStatus;
use App\Enums\ProjectRole;
use App\Models\ProjectMembership;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectMembership>
 */
class ProjectMembershipFactory extends Factory
{
    protected $model = ProjectMembership::class;

    public function definition(): array
    {
        return [
            'project_id' => null,
            'user_id' => null,
            'project_role' => ProjectRole::Developer,
            'status' => MembershipStatus::Active,
            'joined_at' => now(),
            'left_at' => null,
        ];
    }
}
