<?php

declare(strict_types=1);

namespace App\Data\Dashboard;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class DashboardPageData extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        #[DataCollectionOf(DashboardMetricData::class)]
        public readonly Collection $metrics,
        #[DataCollectionOf(DashboardAlertData::class)]
        public readonly Collection $rotationAlerts,
        #[DataCollectionOf(DashboardAlertData::class)]
        public readonly Collection $accessAlerts,
        #[DataCollectionOf(DashboardAlertData::class)]
        public readonly Collection $ownershipAlerts,
        #[DataCollectionOf(DashboardAlertData::class)]
        public readonly Collection $reviewAlerts,
        #[DataCollectionOf(DashboardAlertData::class)]
        public readonly Collection $inboxAlerts,
    ) {
    }
}
