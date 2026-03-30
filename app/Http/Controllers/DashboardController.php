<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\Dashboard\DashboardAlertData;
use App\Data\Dashboard\DashboardMetricData;
use App\Data\Dashboard\DashboardPageData;
use App\Models\AccessGrant;
use App\Models\InboxItem;
use App\Models\Project;
use App\Models\Resource;
use App\Models\ReviewTask;
use App\Models\Secret;
use App\Models\User;
use Inertia\Response;

final readonly class DashboardController
{
    public function __invoke(): Response
    {
        $rotationAlerts = Secret::query()
            ->whereNotNull('rotation_due_at')
            ->where('rotation_due_at', '<=', now()->addDays(30))
            ->orderBy('rotation_due_at')
            ->limit(5)
            ->get()
            ->map(fn(Secret $secret): DashboardAlertData => new DashboardAlertData(
                title: $secret->name,
                description: 'Ротация до ' . $secret->rotation_due_at?->toDateString(),
                href: route('secrets.index'),
            ));

        $accessAlerts = AccessGrant::query()
            ->with(['user', 'resource'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays(30))
            ->where('status', '!=', 'revoked')
            ->orderBy('expires_at')
            ->limit(5)
            ->get()
            ->map(fn(AccessGrant $grant): DashboardAlertData => new DashboardAlertData(
                title: $grant->user->name . ' -> ' . $grant->resource->name,
                description: 'Истекает ' . $grant->expires_at?->toDateString(),
                href: route('access-grants.index'),
            ));

        $ownershipAlerts = Secret::query()
            ->whereNull('owner_user_id')
            ->limit(3)
            ->get()
            ->map(fn(Secret $secret): DashboardAlertData => new DashboardAlertData(
                title: $secret->name,
                description: 'Секрет без владельца',
                href: route('secrets.index'),
            ))
            ->concat(
                User::query()
                    ->where('status', 'inactive')
                    ->whereHas('projectMemberships')
                    ->limit(3)
                    ->get()
                    ->map(fn(User $user): DashboardAlertData => new DashboardAlertData(
                        title: $user->name,
                        description: 'Неактивный пользователь всё ещё привязан к проектам',
                        href: route('users.index'),
                    )),
            );

        $reviewAlerts = ReviewTask::query()
            ->where('status', '!=', 'completed')
            ->where('due_at', '<=', now()->addDays(14))
            ->orderBy('due_at')
            ->limit(5)
            ->get()
            ->map(fn(ReviewTask $task): DashboardAlertData => new DashboardAlertData(
                title: $task->task_type->value,
                description: 'Срок до ' . $task->due_at->toDateString(),
                href: route('reviews.index'),
            ));

        $inboxAlerts = InboxItem::query()
            ->whereIn('status', ['new', 'triaged'])
            ->limit(5)
            ->get()
            ->map(fn(InboxItem $item): DashboardAlertData => new DashboardAlertData(
                title: 'Запись инбокса ' . $item->id,
                description: 'Статус ' . $item->status->value,
                href: route('inbox.index'),
            ));

        return inertia('Dashboard', new DashboardPageData(
            title: 'Дашборд',
            description: 'Живые сигналы риска по доступам, секретам, ревью, инбоксу и ownership gaps.',
            metrics: collect([
                new DashboardMetricData('Проекты', Project::query()->count(), 'Корневые сущности реестра'),
                new DashboardMetricData('Пользователи', User::query()->count(), 'Люди с доступом в приложение'),
                new DashboardMetricData('Ресурсы', Resource::query()->count(), 'Внешние и внутренние системы под контролем'),
                new DashboardMetricData('Доступы', AccessGrant::query()->count(), 'Связи реального доступа к ресурсам'),
                new DashboardMetricData('Секреты', Secret::query()->count(), 'Metadata-first записи секретов'),
            ]),
            rotationAlerts: $rotationAlerts,
            accessAlerts: $accessAlerts,
            ownershipAlerts: $ownershipAlerts,
            reviewAlerts: $reviewAlerts,
            inboxAlerts: $inboxAlerts,
        )->toArray());
    }
}
