<?php

declare(strict_types=1);

namespace App\Http\Controllers\Access;

use App\Actions\Access\RevokeAccessGrantAction;
use App\Actions\Access\StoreAccessGrantAction;
use App\Data\Access\AccessGrantData;
use App\Data\Access\AccessIndexPageData;
use App\Data\Access\StoreAccessGrantData;
use App\Models\AccessGrant;
use App\Models\Project;
use App\Models\ProjectEnvironment;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;

final readonly class AccessGrantController
{
    public function index(): Response
    {
        $actor = user();

        Gate::forUser($actor)->authorize('viewAny', AccessGrant::class);

        $accessGrants = AccessGrant::query()
            ->with(['project', 'environment', 'resource', 'user', 'ownerUser', 'grantedByUser'])
            ->orderByDesc('created_at')
            ->get();

        return inertia(
            'Accesses/Index',
            new AccessIndexPageData(
                canManage: $actor->can('create', AccessGrant::class),
                projectOptions: Project::query()->orderBy('name')->get(['id', 'name', 'key'])
                    ->map(fn(Project $project): array => [
                        'value' => $project->id,
                        'label' => "$project->name ($project->key)",
                    ])->all(),
                environmentOptions: ProjectEnvironment::query()->with('project')->orderBy('name')->get()
                    ->map(fn(ProjectEnvironment $environment): array => [
                        'value' => $environment->id,
                        'label' => "{$environment->project->name} / $environment->name",
                        'project_id' => $environment->project_id,
                    ])->all(),
                resourceOptions: Resource::query()->orderBy('name')->get(['id', 'name', 'provider'])
                    ->map(fn(Resource $resource): array => [
                        'value' => $resource->id,
                        'label' => "$resource->name ($resource->provider)",
                    ])->all(),
                userOptions: User::query()->orderBy('name')->get(['id', 'name', 'email'])
                    ->map(fn(User $user): array => [
                        'value' => $user->id,
                        'label' => "$user->name ($user->email)",
                    ])->all(),
                accessGrants: AccessGrantData::collect($accessGrants),
            )->toArray(),
        );
    }

    public function store(StoreAccessGrantData $data, StoreAccessGrantAction $storeAccessGrant): RedirectResponse
    {
        $actor = user();

        Gate::forUser($actor)->authorize('create', AccessGrant::class);

        $storeAccessGrant->handle(
            actor: $actor,
            data: $data,
        );

        return to_route('access-grants.index');
    }

    public function revoke(AccessGrant $accessGrant, RevokeAccessGrantAction $revokeAccessGrant): RedirectResponse
    {
        $actor = user();

        Gate::forUser($actor)->authorize('revoke', $accessGrant);

        $revokeAccessGrant->handle(actor: $actor, grant: $accessGrant);

        return to_route('access-grants.index');
    }
}
