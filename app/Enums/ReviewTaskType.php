<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum ReviewTaskType: string
{
    use InteractsWithEnumOptions;

    case AccessReview = 'access_review';
    case SecretVerification = 'secret_verification';
    case SecretRotation = 'secret_rotation';
    case OwnershipReview = 'ownership_review';
}
