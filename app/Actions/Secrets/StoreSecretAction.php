<?php

declare(strict_types=1);

namespace App\Actions\Secrets;

use App\Data\Secrets\StoreSecretData;
use App\Models\Secret;

final readonly class StoreSecretAction
{
    public function handle(StoreSecretData $data): Secret
    {
        $secret = Secret::query()->create([
            'project_id' => $data->projectId,
            'environment_id' => $data->environmentId,
            'resource_id' => $data->resourceId,
            'name' => $data->name,
            'secret_type' => $data->secretType,
            'sensitivity' => $data->sensitivity,
            'storage_mode' => $data->storageMode,
            'external_reference' => $data->externalReference,
            'encrypted_value' => $data->encryptedValue,
            'owner_user_id' => $data->ownerUserId,
            'status' => $data->status,
            'reveal_policy' => $data->revealPolicy,
            'rotation_due_at' => $data->rotationDueAt,
            'notes' => $data->notes,
        ]);

        return $secret->refresh();
    }
}
