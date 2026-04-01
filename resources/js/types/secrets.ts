export interface SecretConsumer {
    id: string;
    user_id: string | null;
    user_name: string | null;
    resource_id: string | null;
    environment_id: string | null;
    usage_note: string | null;
}

export interface RevealedSecret {
    id: string;
    name: string;
    value: string | null;
    reason: string | null;
    is_reference_only: boolean;
    external_reference: string | null;
}

export interface Secret {
    id: string;
    project_id: string;
    project_name: string;
    environment_id: string;
    environment_name: string;
    resource_id: string | null;
    name: string;
    secret_type: string;
    sensitivity: string;
    storage_mode: string;
    external_reference: string | null;
    has_encrypted_value: boolean;
    owner_user_id: string | null;
    owner_user_name: string | null;
    status: string;
    reveal_policy: string;
    last_verified_at: string | null;
    last_rotated_at: string | null;
    rotation_due_at: string | null;
    notes: string | null;
    consumers: SecretConsumer[];
}

export interface SecretsIndexPageProps {
    can_manage: boolean;
    can_reveal: boolean;
    default_owner_user_id: string | null;
    project_options: { value: string; label: string }[];
    environment_options: { value: string; label: string; project_id: string }[];
    resource_options: { value: string; label: string }[];
    user_options: { value: string; label: string }[];
    secrets: Secret[];
}
