<?php

declare(strict_types=1);

use App\Enums\AppPermission;
use App\Enums\AppRole;
use App\Enums\ProjectRole;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('project roles enum still matches the supported project role matrix', function (): void {
    expect(ProjectRole::values())->toBe([
        'owner',
        'tech_lead',
        'manager',
        'developer',
    ])->and(ProjectRole::options())->toBe([
        'owner' => 'Владелец',
        'tech_lead' => 'Техлид',
        'manager' => 'Менеджер',
        'developer' => 'Разработчик',
    ]);
});

test('app roles enum now represents product access roles', function (): void {
    expect(AppRole::values())->toBe([
        'owner',
        'administrator',
        'manager',
        'developer',
    ])->and(AppRole::options())->toBe([
        'owner' => 'Владелец',
        'administrator' => 'Администратор',
        'manager' => 'Менеджер',
        'developer' => 'Разработчик',
    ]);
});

test('user app roles drive project access through permissions', function (): void {
    $this->seed(DatabaseSeeder::class);

    $project = Project::factory()->create();

    $administrator = User::factory()->create();
    $administrator->syncRoles([AppRole::Administrator->value]);

    $developer = User::factory()->create();
    $developer->syncRoles([AppRole::Developer->value]);

    expect($administrator->can(AppPermission::ProjectsUpdate->value))->toBeTrue()
        ->and($administrator->can('update', $project))->toBeTrue()
        ->and($developer->can(AppPermission::ProjectsUpdate->value))->toBeFalse()
        ->and($developer->can('update', $project))->toBeFalse();
});
