<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InboxItemStatus;
use App\Enums\InboxSourceType;
use App\Models\InboxItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InboxItem>
 */
class InboxItemFactory extends Factory
{
    protected $model = InboxItem::class;

    public function definition(): array
    {
        return [
            'source_type' => InboxSourceType::Manual,
            'raw_text' => fake()->sentence(),
            'parsed_summary' => ['note' => fake()->sentence()],
            'suggested_project_id' => null,
            'status' => InboxItemStatus::New,
            'created_by_user_id' => null,
            'normalized_by_user_id' => null,
            'normalized_at' => null,
            'normalized_entities' => null,
            'purged_at' => null,
        ];
    }
}
