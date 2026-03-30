<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum AccessGrantStatus: string
{
    use InteractsWithEnumOptions;

    case Active = 'active';
    case Pending = 'pending';
    case Expired = 'expired';
    case Revoked = 'revoked';
}
