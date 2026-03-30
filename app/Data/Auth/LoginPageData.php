<?php

declare(strict_types=1);

namespace App\Data\Auth;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class LoginPageData extends Data
{
    public function __construct(
        public readonly ?string $status,
    ) {
    }
}
