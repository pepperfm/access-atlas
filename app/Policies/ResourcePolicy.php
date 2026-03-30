<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\AppPermission;
use App\Models\Resource;
use App\Models\User;

class ResourcePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(AppPermission::ResourcesView->value);
    }

    public function view(User $user, Resource $resource): bool
    {
        return $user->hasPermissionTo(AppPermission::ResourcesView->value);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(AppPermission::ResourcesManage->value);
    }

    public function manage(User $user, Resource $resource): bool
    {
        return $user->hasPermissionTo(AppPermission::ResourcesManage->value);
    }
}
