<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum SecretStatus: string
{
    use InteractsWithEnumOptions;

    case Active = 'active';
    case Inactive = 'inactive';
    case Archived = 'archived';
    case Revoked = 'revoked';
}
