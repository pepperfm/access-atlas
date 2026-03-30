<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\Audit\AuditEventData;
use App\Data\Audit\AuditIndexPageData;
use App\Models\AuditEvent;
use Inertia\Response;

final readonly class AuditEventController
{
    public function index(): Response
    {
        abort_unless(auth()->user()?->hasPermissionTo('audit.view'), 403);

        $events = AuditEvent::query()
            ->with(['actorUser', 'project'])
            ->latest('created_at')
            ->limit(100)
            ->get();

        return inertia('Audit/Index', new AuditIndexPageData(
            events: AuditEventData::collect($events),
        )->toArray());
    }
}
