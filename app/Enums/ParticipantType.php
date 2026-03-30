<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum ParticipantType: string
{
    use InteractsWithEnumOptions;

    case Human = 'human';
    case Team = 'team';
    case ServiceAccount = 'service_account';
    case Bot = 'bot';
}
