<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AppPermission;
use App\Enums\AppRole;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class AuthorizationSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        collect(AppPermission::cases())
            ->each(static fn(AppPermission $permission): \Spatie\Permission\Models\Permission => Permission::findOrCreate($permission->value, 'web'));

        collect($this->rolePermissions())
            ->each(function (array $permissions, string $roleName): void {
                $role = Role::findOrCreate($roleName, 'web');
                $role->syncPermissions(array_map(
                    static fn(AppPermission $permission): string => $permission->value,
                    $permissions,
                ));
            });

        $defaultUser = User::query()->firstWhere('email', 'owner@example.com');

        if (!$defaultUser instanceof User) {
            logger()->error('[auth.permissions] Default user is missing during authorization bootstrap.', [
                'email' => 'owner@example.com',
            ]);

            return;
        }

        $defaultUser->syncRoles([AppRole::Owner->value]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @return array<string, list<\App\Enums\AppPermission>>
     */
    private function rolePermissions(): array
    {
        return [
            AppRole::Owner->value => AppPermission::cases(),
            AppRole::Administrator->value => [
                AppPermission::DashboardView,
                AppPermission::ProjectsView,
                AppPermission::ProjectsCreate,
                AppPermission::ProjectsUpdate,
                AppPermission::ResourcesView,
                AppPermission::ResourcesManage,
                AppPermission::AccessGrantsView,
                AppPermission::AccessGrantsManage,
                AppPermission::SecretsViewMetadata,
                AppPermission::SecretsManage,
                AppPermission::SecretsReveal,
                AppPermission::ReviewsView,
                AppPermission::ReviewsManage,
                AppPermission::InboxView,
                AppPermission::InboxManage,
                AppPermission::AuditView,
                AppPermission::OffboardingView,
                AppPermission::OffboardingManage,
            ],
            AppRole::Manager->value => [
                AppPermission::DashboardView,
                AppPermission::ProjectsView,
                AppPermission::ProjectsCreate,
                AppPermission::ProjectsUpdate,
                AppPermission::ResourcesView,
                AppPermission::AccessGrantsView,
                AppPermission::AccessGrantsManage,
                AppPermission::SecretsViewMetadata,
                AppPermission::SecretsManage,
                AppPermission::ReviewsView,
                AppPermission::InboxView,
                AppPermission::OffboardingView,
            ],
            AppRole::Developer->value => [
                AppPermission::DashboardView,
                AppPermission::ProjectsView,
                AppPermission::ResourcesView,
                AppPermission::AccessGrantsView,
                AppPermission::SecretsViewMetadata,
                AppPermission::ReviewsView,
                AppPermission::AuditView,
                AppPermission::OffboardingView,
            ],
        ];
    }
}
