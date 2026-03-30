<?php

declare(strict_types=1);

namespace App\Data\Projects;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ProjectsIndexPageData extends Data
{
    public function __construct(
        public readonly bool $canCreate,
        #[DataCollectionOf(ProjectData::class)]
        public readonly Collection $projects,
    ) {
    }
}
