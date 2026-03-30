<?php

declare(strict_types=1);

namespace App\Data\Dashboard;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class DashboardAlertData extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly string $href,
    ) {
    }
}
