<?php

declare(strict_types=1);

namespace App\Actions\Inbox;

use App\Data\Inbox\StoreInboxItemData;
use App\Enums\InboxItemStatus;
use App\Models\InboxItem;
use App\Models\User;

final readonly class StoreInboxItemAction
{
    public function handle(User $actor, StoreInboxItemData $data): InboxItem
    {
        return InboxItem::query()->create([
            'source_type' => $data->sourceType,
            'raw_text' => $data->rawText,
            'suggested_project_id' => $data->suggestedProjectId,
            'status' => InboxItemStatus::New,
            'created_by_user_id' => $actor->id,
        ]);
    }
}
