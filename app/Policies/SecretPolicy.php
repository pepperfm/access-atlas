<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\AppPermission;
use App\Models\Secret;
use App\Models\User;

class SecretPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(AppPermission::SecretsViewMetadata->value);
    }

    public function view(User $user, Secret $secret): bool
    {
        return $user->hasPermissionTo(AppPermission::SecretsViewMetadata->value);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(AppPermission::SecretsManage->value);
    }

    public function reveal(User $user, Secret $secret): bool
    {
        return $user->hasPermissionTo(AppPermission::SecretsReveal->value);
    }
}
