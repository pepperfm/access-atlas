export interface InboxItem {
    id: string;
    source_type: string;
    raw_text: string | null;
    parsed_summary: Record<string, unknown>;
    suggested_project_id: string | null;
    status: string;
    created_by_user_id: string | null;
    normalized_by_user_id: string | null;
    normalized_at: string | null;
    normalized_entities: Record<string, unknown>[];
    purged_at: string | null;
}

export interface InboxIndexPageProps {
    can_manage: boolean;
    project_options: { value: string; label: string }[];
    environment_options: { value: string; label: string; project_id: string }[];
    user_options: { value: string; label: string }[];
    items: InboxItem[];
}
