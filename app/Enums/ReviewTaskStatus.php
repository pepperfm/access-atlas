<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum ReviewTaskStatus: string
{
    use InteractsWithEnumOptions;

    case Open = 'open';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Dismissed = 'dismissed';
}
