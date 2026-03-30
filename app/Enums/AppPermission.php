<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractsWithEnumOptions;

enum AppPermission: string
{
    use InteractsWithEnumOptions;

    case DashboardView = 'dashboard.view';
    case ProjectsView = 'projects.view';
    case ProjectsCreate = 'projects.create';
    case ProjectsUpdate = 'projects.update';
    case ProjectsArchive = 'projects.archive';
    case ResourcesView = 'resources.view';
    case ResourcesManage = 'resources.manage';
    case AccessGrantsView = 'access_grants.view';
    case AccessGrantsManage = 'access_grants.manage';
    case AccessGrantsReview = 'access_grants.review';
    case SecretsViewMetadata = 'secrets.view_metadata';
    case SecretsManage = 'secrets.manage';
    case SecretsReveal = 'secrets.reveal';
    case SecretsRotate = 'secrets.rotate';
    case ReviewsView = 'reviews.view';
    case ReviewsManage = 'reviews.manage';
    case InboxView = 'inbox.view';
    case InboxManage = 'inbox.manage';
    case InboxPurge = 'inbox.purge';
    case AuditView = 'audit.view';
    case OffboardingView = 'offboarding.view';
    case OffboardingManage = 'offboarding.manage';
    case RolesPermissionsManage = 'roles_permissions.manage';
}
