<?php

declare(strict_types=1);

namespace App\Data\Secrets;

use Spatie\LaravelData\Data;
use App\Models\Secret;
use App\Enums\SecretStorageMode;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class RevealSecretData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $value,
        public readonly ?string $reason,
        public readonly bool $isReferenceOnly,
        public readonly ?string $externalReference,
    ) {
    }

    public static function fromSecret(Secret $secret, ?string $reason = null): self
    {
        return new self(
            id: $secret->id,
            name: $secret->name,
            value: $secret->storage_mode === SecretStorageMode::EncryptedValue ? $secret->encrypted_value : null,
            reason: $reason,
            isReferenceOnly: $secret->storage_mode === SecretStorageMode::ExternalReference,
            externalReference: $secret->external_reference,
        );
    }
}
