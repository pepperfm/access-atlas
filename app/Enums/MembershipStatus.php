<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum MembershipStatus: string
{
    use InteractsWithEnumOptions;

    case Active = 'active';
    case Pending = 'pending';
    case Inactive = 'inactive';
    case Left = 'left';
}
