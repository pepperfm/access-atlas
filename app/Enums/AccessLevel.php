<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum AccessLevel: string
{
    use InteractsWithEnumOptions;

    case Owner = 'owner';
    case Admin = 'admin';
    case Write = 'write';
    case Read = 'read';
    case Custom = 'custom';
}
