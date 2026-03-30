<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inbox;

use App\Actions\Inbox\NormalizeInboxItemAction;
use App\Actions\Inbox\PurgeInboxItemAction;
use App\Actions\Inbox\StoreInboxItemAction;
use App\Data\Inbox\InboxIndexPageData;
use App\Data\Inbox\InboxItemData;
use App\Data\Inbox\NormalizeInboxItemData;
use App\Data\Inbox\StoreInboxItemData;
use App\Models\InboxItem;
use App\Models\Project;
use App\Models\ProjectEnvironment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

final readonly class InboxItemController
{
    public function index(): Response
    {
        $items = InboxItem::query()->latest()->get();

        return inertia(
            'Inbox/Index',
            new InboxIndexPageData(
                canManage: auth()->check(),
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
                userOptions: User::query()->orderBy('name')->get(['id', 'name', 'email'])
                    ->map(fn(User $user): array => [
                        'value' => $user->id,
                        'label' => "$user->name ($user->email)",
                    ])->all(),
                items: InboxItemData::collect($items),
            )->toArray(),
        );
    }

    public function store(StoreInboxItemData $data, StoreInboxItemAction $storeInboxItemAction): RedirectResponse
    {
        $actor = user();

        $storeInboxItemAction->handle($actor, $data);

        return to_route('inbox.index');
    }

    public function normalize(
        NormalizeInboxItemData $data,
        InboxItem $inboxItem,
        NormalizeInboxItemAction $normalizeInboxItemAction,
    ): RedirectResponse {
        $actor = user();

        $normalizeInboxItemAction->handle($actor, $inboxItem, $data);

        return to_route('inbox.index');
    }

    public function purge(InboxItem $inboxItem, PurgeInboxItemAction $purgeInboxItemAction): RedirectResponse
    {
        $purgeInboxItemAction->handle($inboxItem);

        return to_route('inbox.index');
    }
}
