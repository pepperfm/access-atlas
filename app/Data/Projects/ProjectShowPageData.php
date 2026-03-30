<?php

declare(strict_types=1);

namespace App\Data\Projects;

use App\Data\Secrets\SecretData;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ProjectShowPageData extends Data
{
    public function __construct(
        public readonly bool $canManage,
        public readonly bool $canArchive,
        public readonly bool $canRevealSecrets,
        public readonly ProjectData $project,
        #[DataCollectionOf(ProjectParticipantOptionData::class)]
        public readonly Collection $userOptions,
        #[DataCollectionOf(SecretData::class)]
        public readonly Collection $secrets,
    ) {
    }
}
