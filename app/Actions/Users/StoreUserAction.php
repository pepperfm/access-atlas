<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Data\Users\StoreUserData;
use App\Models\User;

final readonly class StoreUserAction
{
    public function handle(StoreUserData $data): User
    {
        $user = User::query()->create([
            'name' => str($data->name)->trim()->value(),
            'email' => str($data->email)->trim()->lower()->value(),
            'password' => $data->password,
            'status' => $data->status,
        ]);

        $user->syncRoles([$data->role->value]);

        logger()->info('[users.store] User created.', [
            'user_id' => $user->id,
            'role' => $data->role->value,
        ]);

        return $user->refresh()->load('roles');
    }
}
