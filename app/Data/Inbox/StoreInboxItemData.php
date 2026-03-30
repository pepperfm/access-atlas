<?php

declare(strict_types=1);

namespace App\Data\Inbox;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use App\Enums\InboxSourceType;
use Illuminate\Validation\Rule;

#[MapName(SnakeCaseMapper::class)]
final class StoreInboxItemData extends Data
{
    public function __construct(
        public readonly InboxSourceType $sourceType,
        public readonly string $rawText,
        public readonly ?string $suggestedProjectId,
    ) {
    }

    public static function rules(): array
    {
        return [
            'sourceType' => ['required', 'string', Rule::enum(InboxSourceType::class)],
            'rawText' => ['required', 'string'],
            'suggestedProjectId' => ['nullable', 'uuid', 'exists:projects,id'],
        ];
    }
}
