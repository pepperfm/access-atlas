<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum ReviewTaskTargetType: string
{
    use InteractsWithEnumOptions;

    case AccessGrant = 'access_grant';
    case Secret = 'secret';
    case Resource = 'resource';
}
