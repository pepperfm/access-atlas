<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum SecretRevealPolicy: string
{
    use InteractsWithEnumOptions;

    case SecurityAdminOnly = 'security_admin_only';
    case ProjectOwnerOrSecurityAdmin = 'project_owner_or_security_admin';
    case ProjectManagerOrHigher = 'project_manager_or_higher';
    case CustomManualRule = 'custom_manual_rule';
}
