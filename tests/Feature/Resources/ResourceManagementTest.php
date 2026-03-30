<?php

declare(strict_types=1);

use App\Actions\Resources\LinkResourceToProjectAction;
use App\Actions\Resources\StoreResourceAction;
use App\Data\Resources\LinkProjectResourceData;
use App\Data\Resources\StoreResourceData;
use App\Models\AuditEvent;
use App\Models\Project;
use App\Models\Resource;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('resources can be created and linked to a project with audit events', function (): void {
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

    $resource = app(StoreResourceAction::class)->handle(
        actor: $actor,
        data: StoreResourceData::from([
            'provider' => 'github',
            'kind' => 'repository',
            'name' => 'access-atlas',
            'external_identifier' => 'pepperfm/access-atlas',
            'owner_user_id' => $actor->id,
            'sensitivity' => 'high',
            'canonical_source' => 'github',
            'notes' => 'Primary repository',
        ]),
    );

    $projectLink = app(LinkResourceToProjectAction::class)->handle(
        actor: $actor,
        data: LinkProjectResourceData::from([
            'project_id' => $project->id,
            'resource_id' => $resource->id,
            'environment_id' => null,
            'relation_type' => 'primary',
            'status' => 'active',
        ]),
    );

    expect($resource)->toBeInstanceOf(Resource::class)
        ->and($projectLink->project_id)->toBe($project->id)
        ->and(AuditEvent::query()->where('target_id', $resource->id)->exists())->toBeTrue()
        ->and(AuditEvent::query()->where('target_id', $projectLink->id)->exists())->toBeTrue();
});
