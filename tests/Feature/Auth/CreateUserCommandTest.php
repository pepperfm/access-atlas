<?php

declare(strict_types=1);

use App\Enums\AppRole;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('users:create command creates a user with role and status', function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->artisan('users:create', [
        '--name' => 'Console Manager',
        '--email' => 'console-manager@example.com',
        '--password' => 'password123',
        '--role' => 'manager',
        '--status' => 'inactive',
        '--force' => true,
    ])->assertSuccessful();

    $user = User::query()->firstWhere('email', 'console-manager@example.com');

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Console Manager')
        ->and($user->status->value)->toBe('inactive')
        ->and($user->hasRole(AppRole::Manager->value))->toBeTrue();
});
