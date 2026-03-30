<?php

declare(strict_types=1);

namespace App\Data\Reviews;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class CompleteReviewTaskData extends Data
{
    public function __construct(
        public readonly string $result,
        public readonly ?string $comment,
    ) {
    }

    public static function rules(): array
    {
        return [
            'result' => ['required', 'string', 'max:255'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
