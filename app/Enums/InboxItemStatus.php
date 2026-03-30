<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum InboxItemStatus: string
{
    use InteractsWithEnumOptions;

    case New = 'new';
    case Triaged = 'triaged';
    case Normalized = 'normalized';
    case Dismissed = 'dismissed';
    case Purged = 'purged';
}
