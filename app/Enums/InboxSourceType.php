<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum InboxSourceType: string
{
    use InteractsWithEnumOptions;

    case Manual = 'manual';
    case Chat = 'chat';
    case PastedText = 'pasted_text';
    case Import = 'import';
}
