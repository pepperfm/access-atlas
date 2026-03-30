<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum SecretStorageMode: string
{
    use InteractsWithEnumOptions;

    case EncryptedValue = 'encrypted_value';
    case ExternalReference = 'external_reference';
}
