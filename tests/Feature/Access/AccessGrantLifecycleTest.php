<?php

declare(strict_types=1);

use App\Actions\Access\RevokeAccessGrantAction;
use App\Actions\Access\StoreAccessGrantAction;
use App\Actions\Resources\StoreResourceAction;
use App\Data\Access\StoreAccessGrantData;
use App\Data\Resources\StoreResourceData;
use App\Enums\AccessGrantStatus;
use App\Models\AccessGrant;
use App\Models\AuditEvent;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('access grants can be created and revoked with audit trail', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');
    $project = Project::query()->firstWhere('key', 'access-atlas');

    if (!$project instanceof Project) {
        actingAs($actor);

        post(route('projects.store'), [
            'name' => 'Access Atlas',
            'repository_url' => null,
            'description' => 'Registry project',
            'criticality' => 'high',
        ]);

        $project = Project::query()->firstWhere('key', 'access-atlas');
    }

    $serviceUser = User::factory()->create([
        'name' => 'atlas-bot',
        'email' => 'atlas-bot@example.com',
    ]);

    $resource = app(StoreResourceAction::class)->handle(
        actor: $actor,
        data: StoreResourceData::from([
            'provider' => 'aws',
            'kind' => 'account',
            'name' => 'atlas-aws',
            'external_identifier' => '1234567890',
            'owner_user_id' => $actor->id,
            'sensitivity' => 'critical',
            'canonical_source' => 'aws-organizations',
            'notes' => null,
        ]),
    );

    $grant = app(StoreAccessGrantAction::class)->handle(
        actor: $actor,
        data: StoreAccessGrantData::from([
            'project_id' => $project->id,
            'environment_id' => null,
            'resource_id' => $resource->id,
            'user_id' => $serviceUser->id,
            'grant_kind' => 'token',
            'access_level' => 'admin',
            'scope_description' => 'deploy pipeline',
            'owner_user_id' => $actor->id,
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'review_due_at' => now()->addWeek(),
            'notes' => 'temporary bootstrap grant',
        ]),
    );

    expect($grant->status)->toBe(AccessGrantStatus::Active);

    $revokedGrant = app(RevokeAccessGrantAction::class)->handle(
        actor: $actor,
        grant: $grant,
    );

    expect($grant)->toBeInstanceOf(AccessGrant::class)
        ->and($revokedGrant->status)->toBe(AccessGrantStatus::Revoked)
        ->and($revokedGrant->revoked_at)->not()->toBeNull()
        ->and(AuditEvent::query()->where('target_id', $grant->id)->count())->toBe(2);
});
