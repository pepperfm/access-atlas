<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\AppPermission;
use App\Models\AccessGrant;
use App\Models\User;

class AccessGrantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(AppPermission::AccessGrantsView->value);
    }

    public function view(User $user, AccessGrant $grant): bool
    {
        return $user->hasPermissionTo(AppPermission::AccessGrantsView->value);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(AppPermission::AccessGrantsManage->value);
    }

    public function revoke(User $user, AccessGrant $grant): bool
    {
        return $user->hasPermissionTo(AppPermission::AccessGrantsManage->value);
    }
}
