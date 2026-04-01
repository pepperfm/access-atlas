import type { DashboardAlert, DashboardMetric } from './pages';

export type SearchResultType =
    | 'project'
    | 'user'
    | 'resource'
    | 'access'
    | 'secret';

export interface SearchResult {
    type: SearchResultType;
    id: string;
    title: string;
    subtitle: string;
    href: string;
}

export interface AuditEventRecord {
    id: string;
    action: string;
    target_type: string;
    target_id: string;
    project_name: string | null;
    actor_name: string | null;
    created_at: string | null;
}

export interface AuditIndexPageProps {
    events: AuditEventRecord[];
}

export interface OffboardingParticipantOption {
    id: string;
    name: string;
    status: string;
}

export interface OffboardingChecklistItem {
    title: string;
    description: string;
    href: string;
}

export interface OffboardingSummary {
    user_id: string;
    user_name: string;
    status: string;
    memberships: OffboardingChecklistItem[];
    access_grants: OffboardingChecklistItem[];
    owned_resources: OffboardingChecklistItem[];
    owned_secrets: OffboardingChecklistItem[];
    assigned_reviews: OffboardingChecklistItem[];
}

export interface OffboardingPageProps {
    users: OffboardingParticipantOption[];
    summary: OffboardingSummary | null;
}

export interface DashboardPageProps {
    title: string;
    description: string;
    metrics: DashboardMetric[];
    rotation_alerts: DashboardAlert[];
    access_alerts: DashboardAlert[];
    ownership_alerts: DashboardAlert[];
    review_alerts: DashboardAlert[];
    inbox_alerts: DashboardAlert[];
}
