<?php

declare(strict_types=1);

use App\Enums\AppRole;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Inertia\Testing\AssertableInertia as Assert;

test('owner can view users index', function (): void {
    $this->withoutVite();
    $this->seed(DatabaseSeeder::class);

    $owner = User::query()->firstWhere('email', 'owner@example.com');

    actingAs($owner);

    get(route('users.index'))
        ->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Users/Index')
            ->where('can_manage', true)
            ->has('users')
            ->has('role_options')
        );
});

test('owner can create a user with an app role and update it later', function (): void {
    $this->seed(DatabaseSeeder::class);

    $owner = User::query()->firstWhere('email', 'owner@example.com');

    actingAs($owner);

    post(route('users.store'), [
        'name' => 'Administrator User',
        'email' => 'administrator@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'administrator',
        'status' => 'active',
    ])->assertRedirect(route('users.index'));

    $user = User::query()->firstWhere('email', 'administrator@example.com');

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->hasRole(AppRole::Administrator->value))->toBeTrue();

    put(route('users.update', $user), [
        'role' => 'manager',
        'status' => 'inactive',
    ])->assertRedirect(route('users.index'));

    expect($user->fresh()->hasRole(AppRole::Manager->value))->toBeTrue()
        ->and($user->fresh()->status->value)->toBe('inactive');
});
