<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum SensitivityLevel: string
{
    use InteractsWithEnumOptions;

    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Critical = 'critical';
}
