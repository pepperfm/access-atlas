<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Users\StoreUserAction;
use App\Actions\Users\UpdateUserRoleAction;
use App\Enums\AppPermission;
use App\Data\Users\StoreUserData;
use App\Data\Users\UpdateUserRoleData;
use App\Data\Users\UserData;
use App\Data\Users\UsersIndexPageData;
use App\Enums\AppRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

final readonly class UserController
{
    public function index(): Response
    {
        $actor = user();

        abort_unless($actor->can(AppPermission::RolesPermissionsManage->value), 403);

        return inertia(
            'Users/Index',
            new UsersIndexPageData(
                canManage: $actor->can(AppPermission::RolesPermissionsManage->value),
                users: UserData::collect(User::query()->with('roles')->orderBy('name')->get()),
                roleOptions: collect(AppRole::cases())
                    ->map(static fn(AppRole $role): array => [
                        'label' => $role->label(),
                        'value' => $role->value,
                    ])->all(),
            )->toArray(),
        );
    }

    public function store(StoreUserData $data, StoreUserAction $storeUser): RedirectResponse
    {
        $actor = user();

        abort_unless($actor->can(AppPermission::RolesPermissionsManage->value), 403);

        $storeUser->handle($data);

        return to_route('users.index');
    }

    public function update(User $managedUser, UpdateUserRoleData $data, UpdateUserRoleAction $updateUserRole): RedirectResponse
    {
        $actor = user();

        abort_unless($actor->can(AppPermission::RolesPermissionsManage->value), 403);

        $updateUserRole->handle($managedUser, $data);

        return to_route('users.index');
    }
}
