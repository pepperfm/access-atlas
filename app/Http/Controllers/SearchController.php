<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\Search\SearchResultData;
use App\Models\AccessGrant;
use App\Models\Project;
use App\Models\Resource;
use App\Models\Secret;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

final readonly class SearchController
{
    public function index(Request $request): JsonResponse
    {
        $query = str($request->string('q')->value())->trim()->value();

        if (blank($query)) {
            return response()->json([
                'results' => [],
            ]);
        }

        return response()->json([
            'results' => $this->search($query)
                ->map(static fn(SearchResultData $result): array => $result->toArray())
                ->values()
                ->all(),
        ]);
    }

    /**
     * @param string $query
     *
     * @return Collection<int, SearchResultData>
     */
    private function search(string $query): Collection
    {
        return collect()
            ->concat($this->projectResults($query))
            ->concat($this->userResults($query))
            ->concat($this->resourceResults($query))
            ->concat($this->accessGrantResults($query))
            ->concat($this->secretResults($query));
    }

    /**
     * @param string $query
     *
     * @return Collection<int, SearchResultData>
     */
    private function projectResults(string $query): Collection
    {
        return Project::query()
            ->where(fn($builder) => $builder
                ->where('name', 'like', "%$query%")
                ->orWhere('key', 'like', "%$query%")
                ->orWhere('description', 'like', "%$query%"))
            ->limit(8)
            ->get()
            ->map(fn(Project $project): SearchResultData => new SearchResultData(
                type: 'project',
                id: $project->id,
                title: $project->name,
                subtitle: $project->key,
                href: route('projects.show', $project),
            ));
    }

    /**
     * @param string $query
     *
     * @return Collection<int, SearchResultData>
     */
    private function userResults(string $query): Collection
    {
        return User::query()
            ->where(fn($builder) => $builder
                ->where('name', 'like', "%$query%")
                ->orWhere('email', 'like', "%$query%"))
            ->limit(8)
            ->get()
            ->map(fn(User $user): SearchResultData => new SearchResultData(
                type: 'user',
                id: $user->id,
                title: $user->name,
                subtitle: $user->email,
                href: route('users.index'),
            ));
    }

    /**
     * @param string $query
     *
     * @return Collection<int, SearchResultData>
     */
    private function resourceResults(string $query): Collection
    {
        return Resource::query()
            ->where(fn($builder) => $builder
                ->where('name', 'like', "%$query%")
                ->orWhere('provider', 'like', "%$query%")
                ->orWhere('kind', 'like', "%$query%")
                ->orWhere('external_identifier', 'like', "%$query%"))
            ->limit(8)
            ->get()
            ->map(fn(Resource $resource): SearchResultData => new SearchResultData(
                type: 'resource',
                id: $resource->id,
                title: $resource->name,
                subtitle: implode(' · ', [$resource->provider, $resource->kind]),
                href: route('resources.show', $resource),
            ));
    }

    /**
     * @param string $query
     *
     * @return Collection<int, SearchResultData>
     */
    private function accessGrantResults(string $query): Collection
    {
        return AccessGrant::query()
            ->with(['user', 'resource'])
            ->where(fn($builder) => $builder
                ->where('access_level', 'like', "%$query%")
                ->orWhere('grant_kind', 'like', "%$query%")
                ->orWhere('scope_description', 'like', "%$query%")
                ->orWhereHas('user', fn($related) => $related->where('name', 'like', "%$query%"))
                ->orWhereHas('resource', fn($related) => $related->where('name', 'like', "%$query%")))
            ->limit(8)
            ->get()
            ->map(fn(AccessGrant $grant): SearchResultData => new SearchResultData(
                type: 'access',
                id: $grant->id,
                title: "{$grant->user->name} -> {$grant->resource->name}",
                subtitle: implode(' · ', [$grant->access_level->value, $grant->status->value]),
                href: route('access-grants.index'),
            ));
    }

    /**
     * @param string $query
     *
     * @return Collection<int, SearchResultData>
     */
    private function secretResults(string $query): Collection
    {
        return Secret::query()
            ->where(fn($builder) => $builder
                ->where('name', 'like', "%$query%")
                ->orWhere('secret_type', 'like', "%$query%")
                ->orWhere('notes', 'like', "%$query%"))
            ->limit(8)
            ->get()
            ->map(fn(Secret $secret): SearchResultData => new SearchResultData(
                type: 'secret',
                id: $secret->id,
                title: $secret->name,
                subtitle: implode(' · ', [$secret->secret_type, $secret->status->value]),
                href: route('secrets.index'),
            ));
    }
}
