<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum EnvironmentStatus: string
{
    use InteractsWithEnumOptions;

    case Active = 'active';
    case Archived = 'archived';
}
