<?php

declare(strict_types=1);

namespace App\Actions\Inbox;

use App\Enums\InboxItemStatus;
use App\Models\InboxItem;

final readonly class PurgeInboxItemAction
{
    public function handle(InboxItem $item): InboxItem
    {
        $item->update([
            'raw_text' => null,
            'status' => InboxItemStatus::Purged,
            'purged_at' => now(),
        ]);

        return $item->refresh();
    }
}
