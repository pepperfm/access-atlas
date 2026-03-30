<?php

declare(strict_types=1);

use App\Enums\MembershipStatus;
use App\Enums\ProjectRole;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function (): void {
    $this->withoutVite();
});

test('owner can create a project with default environments and an owner membership', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');

    expect($actor)->toBeInstanceOf(User::class);

    actingAs($actor);

    $response = post(route('projects.store'), [
        'name' => 'Access Atlas',
        'repository_url' => 'https://github.com/pepperfm/access-atlas',
        'description' => 'Project-first registry.',
        'criticality' => 'high',
    ]);

    $project = Project::query()
        ->with(['environments', 'memberships.user'])
        ->firstWhere('name', 'Access Atlas');

    $response->assertRedirect(route('projects.show', $project));

    expect($project)->toBeInstanceOf(Project::class)
        ->and($project->repository_url)->toBe('https://github.com/pepperfm/access-atlas')
        ->and($project->environments)->toHaveCount(3)
        ->and($project->memberships)->toHaveCount(1)
        ->and($project->memberships->first()?->project_role)->toBe(ProjectRole::Owner);
});

test('authenticated users can view the projects index', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');

    actingAs($actor);

    $response = get(route('projects.index'));

    $response->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Projects/Index')
            ->where('can_create', true)
            ->has('projects'),
        );
});

test('project owners can archive a project', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');

    actingAs($actor);

    post(route('projects.store'), [
        'name' => 'Atlas Core',
        'repository_url' => null,
        'description' => null,
        'criticality' => 'critical',
    ]);

    $project = Project::query()->firstWhere('name', 'Atlas Core');

    $response = post(route('projects.archive', $project));

    $response->assertRedirect(route('projects.show', $project));

    expect($project->fresh()->archived_at)->not()->toBeNull()
        ->and($project->fresh()->status->value)->toBe('archived');
});

test('owners can update a project and attach a user', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');
    $managedUser = User::factory()->create([
        'name' => 'Мария',
        'email' => 'maria@example.com',
    ]);

    actingAs($actor);

    post(route('projects.store'), [
        'name' => 'Ops Atlas',
        'repository_url' => null,
        'description' => null,
        'criticality' => 'medium',
    ]);

    $project = Project::query()->firstWhere('name', 'Ops Atlas');

    $updateResponse = put(route('projects.update', $project), [
        'name' => 'Ops Atlas Updated',
        'repository_url' => 'https://git.example.com/ops-atlas',
        'description' => 'Critical internal platform.',
        'criticality' => 'high',
    ]);

    $updateResponse->assertRedirect(route('projects.show', $project));

    $attachResponse = post(route('projects.users.store', $project), [
        'user_id' => $managedUser->id,
        'project_role' => 'manager',
    ]);

    $attachResponse->assertRedirect(route('projects.show', $project));

    expect($project->fresh()->name)->toBe('Ops Atlas Updated')
        ->and($project->fresh()->repository_url)->toBe('https://git.example.com/ops-atlas')
        ->and($project->memberships()->where('user_id', $managedUser->id)->where('project_role', ProjectRole::Manager)->exists())->toBeTrue();
});

test('owners can remove a participant from a project', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');
    $managedUser = User::factory()->create([
        'name' => 'Удаляемый',
        'email' => 'removable@example.com',
    ]);

    actingAs($actor);

    post(route('projects.store'), [
        'name' => 'Remove Test Project',
        'repository_url' => null,
        'description' => null,
        'criticality' => 'low',
    ]);

    $project = Project::query()->firstWhere('name', 'Remove Test Project');

    post(route('projects.users.store', $project), [
        'user_id' => $managedUser->id,
        'project_role' => 'developer',
    ]);

    expect($project->memberships()->where('user_id', $managedUser->id)->where('status', MembershipStatus::Active)->exists())->toBeTrue();

    delete(route('projects.users.destroy', ['project' => $project->id, 'user' => $managedUser->id]))
        ->assertRedirect(route('projects.show', $project));

    $membership = $project->memberships()->where('user_id', $managedUser->id)->first();

    expect($membership->status)->toBe(MembershipStatus::Left)
        ->and($membership->left_at)->not()->toBeNull();
});
