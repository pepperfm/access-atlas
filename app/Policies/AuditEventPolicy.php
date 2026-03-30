<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\AppPermission;
use App\Models\AuditEvent;
use App\Models\User;

class AuditEventPolicy
{
    public function viewAnyAudit(User $user, ?AuditEvent $_event = null): bool
    {
        return $user->hasPermissionTo(AppPermission::AuditView->value);
    }
}
