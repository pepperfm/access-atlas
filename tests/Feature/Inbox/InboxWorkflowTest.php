<?php

declare(strict_types=1);

use App\Actions\Inbox\NormalizeInboxItemAction;
use App\Actions\Inbox\PurgeInboxItemAction;
use App\Actions\Inbox\StoreInboxItemAction;
use App\Actions\Projects\StoreProjectAction;
use App\Data\Inbox\NormalizeInboxItemData;
use App\Data\Inbox\StoreInboxItemData;
use App\Data\Projects\StoreProjectData;
use App\Enums\InboxItemStatus;
use App\Enums\InboxSourceType;
use App\Enums\ProjectCriticality;
use App\Models\InboxItem;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

test('inbox items can be normalized into structured secret records', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');
    $project = app(StoreProjectAction::class)->handle($actor, StoreProjectData::from([
        'name' => 'Inbox Normalize',
        'description' => null,
        'criticality' => ProjectCriticality::Medium,
    ]));

    $environment = $project->environments()->first();
    $membership = $project->memberships()->first();

    $item = app(StoreInboxItemAction::class)->handle($actor, StoreInboxItemData::from([
        'source_type' => InboxSourceType::Manual,
        'raw_text' => 'Stripe prod secret = sk_live_123',
        'suggested_project_id' => $project->id,
    ]));

    $normalized = app(NormalizeInboxItemAction::class)->handle($actor, $item, NormalizeInboxItemData::from([
        'parsed_summary' => ['provider' => 'stripe', 'kind' => 'api_key'],
        'project_id' => $project->id,
        'environment_id' => $environment->id,
        'name' => 'stripe-prod-secret',
        'secret_type' => 'api_key',
        'storage_mode' => 'encrypted_value',
        'external_reference' => null,
        'encrypted_value' => 'sk_live_123',
        'owner_user_id' => $membership->user_id,
        'reveal_policy' => 'project_owner_or_security_admin',
        'sensitivity' => 'high',
    ]));

    expect($normalized->status)->toBe(InboxItemStatus::Normalized)
        ->and($normalized->normalized_entities)->toHaveCount(1);
});

test('purging an inbox item removes raw text and marks the item as purged', function (): void {
    $this->seed(DatabaseSeeder::class);

    $actor = User::query()->firstWhere('email', 'owner@example.com');

    $item = app(StoreInboxItemAction::class)->handle($actor, StoreInboxItemData::from([
        'source_type' => InboxSourceType::Manual,
        'raw_text' => 'temporary raw secret',
        'suggested_project_id' => null,
    ]));

    $purged = app(PurgeInboxItemAction::class)->handle($item);

    expect($purged)->toBeInstanceOf(InboxItem::class)
        ->and($purged->status)->toBe(InboxItemStatus::Purged)
        ->and($purged->raw_text)->toBeNull()
        ->and($purged->purged_at)->not()->toBeNull();
});
