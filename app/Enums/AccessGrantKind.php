<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum AccessGrantKind: string
{
    use InteractsWithEnumOptions;

    case Membership = 'membership';
    case Role = 'role';
    case Credential = 'credential';
    case Token = 'token';
    case Ownership = 'ownership';
    case Custom = 'custom';
}
