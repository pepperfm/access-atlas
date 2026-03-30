<?php

declare(strict_types=1);

namespace App\Http\Controllers\Secrets;

use App\Actions\Secrets\StoreSecretAction;
use App\Data\Secrets\SecretData;
use App\Data\Secrets\SecretsIndexPageData;
use App\Data\Secrets\StoreSecretData;
use App\Models\Project;
use App\Models\ProjectEnvironment;
use App\Models\Resource;
use App\Models\Secret;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;

final readonly class SecretController
{
    public function index(): Response
    {
        $actor = user();

        Gate::forUser($actor)->authorize('viewAny', Secret::class);

        $secrets = Secret::query()
            ->with(['project', 'environment', 'ownerUser', 'consumers.user'])
            ->latest()
            ->get();

        return inertia(
            'Secrets/Index',
            new SecretsIndexPageData(
                canManage: $actor->can('create', Secret::class),
                canReveal: $actor->hasPermissionTo('secrets.reveal'),
                defaultOwnerUserId: $actor->id,
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
                secrets: SecretData::collect($secrets),
            )->toArray(),
        );
    }

    public function store(StoreSecretData $data, StoreSecretAction $storeSecretAction): RedirectResponse
    {
        Gate::forUser(user())->authorize('create', Secret::class);

        $storeSecretAction->handle($data);

        return to_route('secrets.index');
    }
}
