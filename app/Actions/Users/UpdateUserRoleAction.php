<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Data\Users\UpdateUserRoleData;
use App\Models\User;

final readonly class UpdateUserRoleAction
{
    public function handle(User $user, UpdateUserRoleData $data): User
    {
        $user->update([
            'status' => $data->status,
        ]);

        $user->syncRoles([$data->role->value]);

        logger()->info('[users.update-role] User role updated.', [
            'user_id' => $user->id,
            'role' => $data->role->value,
            'status' => $data->status->value,
        ]);

        return $user->refresh()->load('roles');
    }
}
