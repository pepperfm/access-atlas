<?php

declare(strict_types=1);

namespace App\Actions\Inbox;

use App\Actions\Secrets\StoreSecretAction;
use App\Data\Inbox\NormalizeInboxItemData;
use App\Data\Secrets\StoreSecretData;
use App\Enums\InboxItemStatus;
use App\Enums\SecretRevealPolicy;
use App\Enums\SecretStatus;
use App\Enums\SecretStorageMode;
use App\Enums\SensitivityLevel;
use App\Models\InboxItem;
use App\Models\User;
use Illuminate\Support\Arr;

final readonly class NormalizeInboxItemAction
{
    public function __construct(
        private StoreSecretAction $storeSecretAction,
    ) {
    }

    public function handle(User $actor, InboxItem $item, NormalizeInboxItemData $data): InboxItem
    {
        $normalizedEntities = [];

        if ($data->projectId && $data->environmentId && $data->name && $data->secretType) {
            $secret = $this->storeSecretAction->handle(
                data: new StoreSecretData(
                    projectId: $data->projectId,
                    environmentId: $data->environmentId,
                    resourceId: null,
                    name: $data->name,
                    secretType: $data->secretType,
                    sensitivity: Arr::toEnum(
                        $data->parsedSummary,
                        'sensitivity',
                        SensitivityLevel::class,
                        $data->sensitivity ?? SensitivityLevel::Medium,
                    ),
                    storageMode: Arr::toEnum(
                        $data->parsedSummary,
                        'storage_mode',
                        SecretStorageMode::class,
                        $data->storageMode ?? SecretStorageMode::ExternalReference,
                    ),
                    externalReference: $data->externalReference,
                    encryptedValue: $data->encryptedValue,
                    ownerUserId: $data->ownerUserId,
                    status: SecretStatus::Active,
                    revealPolicy: Arr::toEnum(
                        $data->parsedSummary,
                        'reveal_policy',
                        SecretRevealPolicy::class,
                        $data->revealPolicy
                            ? SecretRevealPolicy::from($data->revealPolicy)
                            : SecretRevealPolicy::ProjectOwnerOrSecurityAdmin,
                    ),
                    rotationDueAt: null,
                    notes: 'Normalized from inbox item ' . $item->id,
                ),
            );

            $normalizedEntities[] = [
                'type' => 'secret',
                'id' => $secret->id,
                'name' => $secret->name,
            ];
        }

        $item->update([
            'parsed_summary' => $data->parsedSummary,
            'suggested_project_id' => $data->projectId ?? $item->suggested_project_id,
            'status' => InboxItemStatus::Normalized,
            'normalized_by_user_id' => $actor->id,
            'normalized_at' => now(),
            'normalized_entities' => $normalizedEntities,
        ]);

        return $item->refresh();
    }
}
