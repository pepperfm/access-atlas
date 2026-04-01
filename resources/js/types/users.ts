export interface ManagedUser {
    id: string;
    name: string;
    email: string;
    status: string;
    role: string | null;
    last_login_at: string | null;
}

export interface UsersIndexPageProps {
    can_manage: boolean;
    users: ManagedUser[];
    role_options: { label: string; value: string }[];
}
