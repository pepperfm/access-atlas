<?php

declare(strict_types=1);

use App\Actions\Projects\StoreProjectAction;
use App\Actions\Secrets\RevealSecretAction;
use App\Actions\Secrets\StoreSecretAction;
use App\Data\Projects\StoreProjectData;
use App\Data\Secrets\StoreSecretData;
use App\Enums\ProjectCriticality;
use App\Enums\SecretRevealPolicy;
use App\Enums\SecretStatus;
use App\Enums\SecretStorageMode;
use App\Enums\SensitivityLevel;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('encrypted secrets are stored without exposing raw values in persistence', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');
    $project = app(StoreProjectAction::class)->handle($actor, StoreProjectData::from([
        'name' => 'Secrets Registry',
        'repository_url' => null,
        'description' => null,
        'criticality' => ProjectCriticality::High,
    ]));

    $environment = $project->environments()->first();
    $ownerMembership = $project->memberships()->first();

    $secret = app(StoreSecretAction::class)->handle(StoreSecretData::from([
        'project_id' => $project->id,
        'environment_id' => $environment->id,
        'resource_id' => null,
        'name' => 'stripe-prod-api-key',
        'secret_type' => 'api_key',
        'sensitivity' => SensitivityLevel::High,
        'storage_mode' => SecretStorageMode::EncryptedValue,
        'external_reference' => null,
        'encrypted_value' => 'top-secret-value',
        'owner_user_id' => $ownerMembership->user_id,
        'status' => SecretStatus::Active,
        'reveal_policy' => SecretRevealPolicy::ProjectOwnerOrSecurityAdmin,
        'rotation_due_at' => null,
        'notes' => null,
    ]));

    expect($secret->encrypted_value)->toBe('top-secret-value')
        ->and(db()->table('secrets')->where('id', $secret->id)->value('encrypted_value'))->not()->toBe('top-secret-value');
});

test('reveal action returns decrypted value for encrypted secrets', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');
    $project = app(StoreProjectAction::class)->handle($actor, StoreProjectData::from([
        'name' => 'Reveal Check',
        'repository_url' => null,
        'description' => null,
        'criticality' => ProjectCriticality::High,
    ]));

    $environment = $project->environments()->first();
    $ownerMembership = $project->memberships()->first();

    $secret = app(StoreSecretAction::class)->handle(StoreSecretData::from([
        'project_id' => $project->id,
        'environment_id' => $environment->id,
        'resource_id' => null,
        'name' => 'slack-bot-token',
        'secret_type' => 'api_key',
        'sensitivity' => SensitivityLevel::High,
        'storage_mode' => SecretStorageMode::EncryptedValue,
        'external_reference' => null,
        'encrypted_value' => 'xoxb-secret',
        'owner_user_id' => $ownerMembership->user_id,
        'status' => SecretStatus::Active,
        'reveal_policy' => SecretRevealPolicy::ProjectOwnerOrSecurityAdmin,
        'rotation_due_at' => null,
        'notes' => null,
    ]));

    $revealed = app(RevealSecretAction::class)->handle($actor, $secret, 'rotation check');

    expect($revealed->value)->toBe('xoxb-secret')
        ->and($revealed->isReferenceOnly)->toBeFalse();
});

test('reveal route flashes revealed secret payload', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');
    $project = app(StoreProjectAction::class)->handle($actor, StoreProjectData::from([
        'name' => 'Reveal Route Check',
        'repository_url' => null,
        'description' => null,
        'criticality' => ProjectCriticality::High,
    ]));

    $environment = $project->environments()->first();
    $ownerMembership = $project->memberships()->first();

    $secret = app(StoreSecretAction::class)->handle(StoreSecretData::from([
        'project_id' => $project->id,
        'environment_id' => $environment->id,
        'resource_id' => null,
        'name' => 'stripe-secret-key',
        'secret_type' => 'api_key',
        'sensitivity' => SensitivityLevel::Medium,
        'storage_mode' => SecretStorageMode::EncryptedValue,
        'external_reference' => null,
        'encrypted_value' => 'sk_live_secret',
        'owner_user_id' => $ownerMembership->user_id,
        'status' => SecretStatus::Active,
        'reveal_policy' => SecretRevealPolicy::ProjectOwnerOrSecurityAdmin,
        'rotation_due_at' => null,
        'notes' => null,
    ]));

    actingAs($actor);

    post(route('secrets.reveal', $secret), [
        'reason' => 'Need to update env locally.',
    ])->assertRedirect()
        ->assertSessionHas('revealed_secret', fn(array $data): bool => $data['name'] === 'stripe-secret-key'
            && $data['value'] === 'sk_live_secret'
            && $data['is_reference_only'] === false);
});

test('reveal route requires reason for high sensitivity secrets', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');
    $project = app(StoreProjectAction::class)->handle($actor, StoreProjectData::from([
        'name' => 'Reveal Reason Check',
        'repository_url' => null,
        'description' => null,
        'criticality' => ProjectCriticality::High,
    ]));

    $environment = $project->environments()->first();
    $ownerMembership = $project->memberships()->first();

    $secret = app(StoreSecretAction::class)->handle(StoreSecretData::from([
        'project_id' => $project->id,
        'environment_id' => $environment->id,
        'resource_id' => null,
        'name' => 'critical-slack-token',
        'secret_type' => 'token',
        'sensitivity' => SensitivityLevel::Critical,
        'storage_mode' => SecretStorageMode::EncryptedValue,
        'external_reference' => null,
        'encrypted_value' => 'xoxb-critical-secret',
        'owner_user_id' => $ownerMembership->user_id,
        'status' => SecretStatus::Active,
        'reveal_policy' => SecretRevealPolicy::ProjectOwnerOrSecurityAdmin,
        'rotation_due_at' => null,
        'notes' => null,
    ]));

    actingAs($actor);

    post(route('secrets.reveal', $secret), [])
        ->assertSessionHasErrors('reason');
});

test('secret store requires raw value when encrypted storage is selected', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');
    $project = app(StoreProjectAction::class)->handle($actor, StoreProjectData::from([
        'name' => 'Encrypted Secret Validation',
        'repository_url' => null,
        'description' => null,
        'criticality' => ProjectCriticality::High,
    ]));

    actingAs($actor);

    $response = from(route('secrets.index'))->post(route('secrets.store'), [
        'project_id' => $project->id,
        'environment_id' => $project->environments()->first()->id,
        'resource_id' => null,
        'name' => 'github-token',
        'secret_type' => 'api_key',
        'sensitivity' => SensitivityLevel::High->value,
        'storage_mode' => SecretStorageMode::EncryptedValue->value,
        'external_reference' => null,
        'encrypted_value' => null,
        'owner_user_id' => $project->memberships()->first()->user_id,
        'status' => SecretStatus::Active->value,
        'reveal_policy' => SecretRevealPolicy::ProjectOwnerOrSecurityAdmin->value,
        'rotation_due_at' => null,
        'notes' => null,
    ]);

    $response->assertSessionHasErrors('encrypted_value');
});

test('secret store requires external reference when external storage is selected', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');
    $project = app(StoreProjectAction::class)->handle($actor, StoreProjectData::from([
        'name' => 'External Secret Validation',
        'repository_url' => null,
        'description' => null,
        'criticality' => ProjectCriticality::High,
    ]));

    actingAs($actor);

    $response = from(route('secrets.index'))->post(route('secrets.store'), [
        'project_id' => $project->id,
        'environment_id' => $project->environments()->first()->id,
        'resource_id' => null,
        'name' => 'prod-1password-link',
        'secret_type' => 'token',
        'sensitivity' => SensitivityLevel::Medium->value,
        'storage_mode' => SecretStorageMode::ExternalReference->value,
        'external_reference' => null,
        'encrypted_value' => null,
        'owner_user_id' => $project->memberships()->first()->user_id,
        'status' => SecretStatus::Active->value,
        'reveal_policy' => SecretRevealPolicy::ProjectOwnerOrSecurityAdmin->value,
        'rotation_due_at' => null,
        'notes' => null,
    ]);

    $response->assertSessionHasErrors('external_reference');
});
