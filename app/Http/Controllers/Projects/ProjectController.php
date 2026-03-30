<?php

declare(strict_types=1);

namespace App\Http\Controllers\Projects;

use App\Actions\Projects\AssignProjectParticipantAction;
use App\Actions\Projects\ArchiveProjectAction;
use App\Actions\Projects\RemoveProjectParticipantAction;
use App\Actions\Projects\StoreProjectAction;
use App\Actions\Projects\UpdateProjectAction;
use App\Data\Projects\AssignProjectParticipantData;
use App\Data\Projects\ProjectData;
use App\Data\Projects\ProjectParticipantOptionData;
use App\Data\Projects\ProjectsIndexPageData;
use App\Data\Projects\ProjectShowPageData;
use App\Data\Projects\StoreProjectData;
use App\Data\Projects\UpdateProjectData;
use App\Data\Secrets\SecretData;
use App\Models\Project;
use App\Models\Secret;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;

final readonly class ProjectController
{
    public function index(): Response
    {
        $actor = user();

        Gate::forUser($actor)->authorize('viewAny', Project::class);

        $projects = Project::query()
            ->with(['environments', 'memberships.user'])
            ->orderBy('name')
            ->get();

        return inertia(
            'Projects/Index',
            new ProjectsIndexPageData(
                canCreate: $actor->can('create', Project::class),
                projects: ProjectData::collect($projects),
            )->toArray(),
        );
    }

    public function show(Project $project): Response
    {
        $actor = user();

        Gate::forUser($actor)->authorize('view', $project);

        $secrets = Secret::query()
            ->where('project_id', $project->id)
            ->with(['project', 'environment', 'ownerUser', 'consumers.user'])
            ->latest()
            ->get();

        return inertia(
            'Projects/Show',
            new ProjectShowPageData(
                canManage: $actor->can('update', $project),
                canArchive: $actor->can('archive', $project),
                canRevealSecrets: $actor->hasPermissionTo('secrets.reveal'),
                project: ProjectData::from($project),
                userOptions: ProjectParticipantOptionData::collect(
                    User::query()->orderBy('name')->get(),
                ),
                secrets: SecretData::collect($secrets),
            )->toArray(),
        );
    }

    public function store(StoreProjectData $data, StoreProjectAction $storeProject): RedirectResponse
    {
        $actor = user();

        Gate::forUser($actor)->authorize('create', Project::class);

        $project = $storeProject->handle(
            actor: $actor,
            data: $data,
        );

        return to_route('projects.show', $project);
    }

    public function update(Project $project, UpdateProjectData $data, UpdateProjectAction $updateProject): RedirectResponse
    {
        $actor = user();

        Gate::forUser($actor)->authorize('update', $project);

        $updateProject->handle($project, $data);

        return to_route('projects.show', $project);
    }

    public function addParticipant(
        Project $project,
        AssignProjectParticipantData $data,
        AssignProjectParticipantAction $assignProjectParticipant,
    ): RedirectResponse {
        $actor = user();

        Gate::forUser($actor)->authorize('update', $project);

        $assignProjectParticipant->handle($project, $data);

        return to_route('projects.show', $project);
    }

    public function removeParticipant(
        Project $project,
        User $user,
        RemoveProjectParticipantAction $removeProjectParticipant,
    ): RedirectResponse {
        $actor = user();

        Gate::forUser($actor)->authorize('update', $project);

        $removeProjectParticipant->handle($project, $user->id);

        return to_route('projects.show', $project);
    }

    public function archive(Project $project, ArchiveProjectAction $archiveProject): RedirectResponse
    {
        $actor = user();

        Gate::forUser($actor)->authorize('archive', $project);

        $archiveProject->handle(project: $project);

        return to_route('projects.show', $project);
    }
}
