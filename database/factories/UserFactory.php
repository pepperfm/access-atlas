<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static::$password ??= bcrypt('password');

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'status' => UserStatus::Active,
            'password' => static::$password,
            'last_login_at' => null,
            'remember_token' => str()->random(10),
        ];
    }
}
