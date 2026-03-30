<?php

declare(strict_types=1);

use App\Enums\AppRole;
use App\Enums\ProjectRole;
use App\Models\AccessGrant;
use App\Models\AuditEvent;
use App\Models\InboxItem;
use App\Models\Project;
use App\Models\ProjectMembership;
use App\Models\Resource;
use App\Models\ReviewTask;
use App\Models\Secret;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Inertia\Testing\AssertableInertia as Assert;

test('dashboard renders live metrics and alert sections', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');
    Project::factory()->create(['created_by_user_id' => $actor->id]);
    Resource::factory()->create(['owner_user_id' => $actor->id]);
    InboxItem::factory()->create(['created_by_user_id' => $actor->id]);

    actingAs($actor);

    $response = get(route('dashboard'));

    $response->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Dashboard')
            ->has('metrics', 5)
            ->has('inbox_alerts'),
        );
});

test('search returns metadata results', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');
    User::factory()->create(['name' => 'Ops Atlas']);
    Project::factory()->create(['name' => 'Atlas Registry', 'created_by_user_id' => $actor->id]);

    actingAs($actor);

    $response = get(route('search.index', ['q' => 'Atlas']));

    $response->assertOk()
        ->assertJsonStructure([
            'results' => [
                '*' => ['type', 'id', 'title', 'subtitle', 'href'],
            ],
        ])
        ->assertJsonPath('results.0.title', 'Atlas Registry');
});

test('audit page lists security events', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');
    $actor->syncRoles([AppRole::Owner->value]);

    AuditEvent::query()->create([
        'actor_user_id' => $actor->id,
        'action' => 'resource.created',
        'target_type' => 'resource',
        'target_id' => str()->uuid()->toString(),
        'project_id' => null,
        'meta' => ['provider' => 'github'],
    ]);

    actingAs($actor);

    $response = get(route('audit.index'));

    $response->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Audit/Index')
            ->has('events', 1),
        );
});

test('offboarding page builds a user checklist', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');
    $project = Project::factory()->create(['created_by_user_id' => $actor->id]);

    ProjectMembership::factory()->create([
        'project_id' => $project->id,
        'user_id' => $actor->id,
        'project_role' => ProjectRole::Owner,
    ]);

    $resource = Resource::factory()->create(['owner_user_id' => $actor->id]);
    AccessGrant::factory()->create([
        'project_id' => $project->id,
        'resource_id' => $resource->id,
        'user_id' => $actor->id,
        'owner_user_id' => $actor->id,
        'granted_by_user_id' => $actor->id,
    ]);

    $environment = $project->environments()->firstOrCreate([
        'key' => 'prod',
    ], [
        'name' => 'Production',
        'is_production' => true,
        'status' => 'active',
        'sort_order' => 30,
    ]);

    Secret::factory()->create([
        'project_id' => $project->id,
        'environment_id' => $environment->id,
        'owner_user_id' => $actor->id,
    ]);

    ReviewTask::factory()->create([
        'assigned_to_user_id' => $actor->id,
    ]);

    actingAs($actor);

    $response = get(route('offboarding.index', ['user_id' => $actor->id]));

    $response->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Offboarding/Index')
            ->where('summary.user_id', $actor->id),
        );
});
