<?php

declare(strict_types=1);

use App\Enums\AppPermission;
use App\Enums\AppRole;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('authorization bootstrap seeds UUID roles and permissions', function (): void {
    $this->seed(DatabaseSeeder::class);

    $roles = Role::query()->get();
    $permissions = Permission::query()->get();

    expect($roles)->toHaveCount(count(AppRole::cases()))
        ->and($permissions)->toHaveCount(count(AppPermission::cases()))
        ->and($roles->every(static fn(Role $role): bool => str($role->id)->isUuid()))->toBeTrue()
        ->and($permissions->every(static fn(Permission $permission): bool => str($permission->id)->isUuid()))->toBeTrue();
});

test('database seeder assigns the owner role to the default user', function (): void {
    $this->seed(DatabaseSeeder::class);

    $user = User::query()->firstWhere('email', 'owner@example.com');

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->hasRole(AppRole::Owner->value))->toBeTrue()
        ->and($user->can(AppPermission::DashboardView->value))->toBeTrue()
        ->and($user->can(AppPermission::RolesPermissionsManage->value))->toBeTrue();
});
