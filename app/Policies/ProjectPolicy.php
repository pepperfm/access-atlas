<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\AppPermission;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(AppPermission::ProjectsView->value);
    }

    public function view(User $user, Project $project): bool
    {
        return $user->hasPermissionTo(AppPermission::ProjectsView->value);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(AppPermission::ProjectsCreate->value);
    }

    public function update(User $user, Project $project): bool
    {
        return $user->hasPermissionTo(AppPermission::ProjectsUpdate->value);
    }

    public function archive(User $user, Project $project): bool
    {
        return $user->hasPermissionTo(AppPermission::ProjectsArchive->value);
    }
}
