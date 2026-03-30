<?php

declare(strict_types=1);

namespace App\Actions\Secrets;

use App\Data\Secrets\RevealSecretData;
use App\Enums\SensitivityLevel;
use App\Models\Secret;
use App\Models\User;
use Illuminate\Validation\ValidationException;

final readonly class RevealSecretAction
{
    public function handle(User $actor, Secret $secret, ?string $reason = null): RevealSecretData
    {
        if (in_array($secret->sensitivity, [SensitivityLevel::High, SensitivityLevel::Critical], true) && blank($reason)) {
            throw ValidationException::withMessages([
                'reason' => 'Для секретов высокой чувствительности нужно указать причину раскрытия.',
            ]);
        }

        $this->recordAuditEvent(
            actor: $actor,
            action: 'secret.revealed',
            targetType: 'secret',
            targetId: $secret->id,
            projectId: $secret->project_id,
            meta: [
                'reason' => $reason,
                'storage_mode' => $secret->storage_mode->value,
                'sensitivity' => $secret->sensitivity->value,
            ],
        );

        return RevealSecretData::fromSecret($secret, $reason);
    }

    /**
     * @param array<string, mixed> $meta
     * @param User $actor
     * @param string $action
     * @param string $targetType
     * @param string $targetId
     * @param ?string $projectId
     */
    private function recordAuditEvent(User $actor, string $action, string $targetType, string $targetId, ?string $projectId, array $meta): void
    {
        if (!class_exists(\App\Models\AuditEvent::class)) {
            return;
        }

        \App\Models\AuditEvent::query()->create([
            'actor_user_id' => $actor->id,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'project_id' => $projectId,
            'meta' => $meta,
        ]);
    }
}
