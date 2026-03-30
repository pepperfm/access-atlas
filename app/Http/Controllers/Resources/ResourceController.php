<?php

declare(strict_types=1);

namespace App\Http\Controllers\Resources;

use App\Actions\Resources\LinkResourceToProjectAction;
use App\Actions\Resources\StoreResourceAction;
use App\Data\Resources\LinkProjectResourceData;
use App\Data\Resources\ResourceData;
use App\Data\Resources\ResourcesIndexPageData;
use App\Data\Resources\ResourceShowPageData;
use App\Data\Resources\StoreResourceData;
use App\Models\Project;
use App\Models\ProjectEnvironment;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;

final readonly class ResourceController
{
    public function index(): Response
    {
        $actor = user();

        Gate::forUser($actor)->authorize('viewAny', Resource::class);

        $resources = Resource::query()
            ->with(['ownerUser', 'projectLinks.project', 'projectLinks.environment'])
            ->orderBy('name')
            ->get();

        return inertia(
            'Resources/Index',
            new ResourcesIndexPageData(
                canManage: $actor->can('create', Resource::class),
                userOptions: User::query()->orderBy('name')->get(['id', 'name', 'email'])
                    ->map(fn(User $user): array => [
                        'value' => $user->id,
                        'label' => "$user->name ($user->email)",
                    ])->all(),
                resources: ResourceData::collect($resources),
            )->toArray(),
        );
    }

    public function show(Resource $resource): Response
    {
        $actor = user();

        Gate::forUser($actor)->authorize('view', $resource);

        return inertia(
            'Resources/Show',
            new ResourceShowPageData(
                canManage: $actor->can('manage', $resource),
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
                resource: ResourceData::from($resource),
            )->toArray(),
        );
    }

    public function store(StoreResourceData $data, StoreResourceAction $storeResource): RedirectResponse
    {
        $actor = user();

        Gate::forUser($actor)->authorize('create', Resource::class);

        $resource = $storeResource->handle(
            actor: $actor,
            data: $data,
        );

        return to_route('resources.show', $resource);
    }

    public function linkToProject(LinkProjectResourceData $data, LinkResourceToProjectAction $linkResourceToProject): RedirectResponse
    {
        $actor = user();
        $resource = Resource::query()->findOrFail($data->resourceId);

        Gate::forUser($actor)->authorize('manage', $resource);

        $projectResource = $linkResourceToProject->handle(actor: $actor, data: $data);

        return to_route('resources.show', $projectResource->resource);
    }
}
