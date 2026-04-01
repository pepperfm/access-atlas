export interface LoginPageProps {
    status?: string | null;
}

export interface DashboardMetric {
    title: string;
    value: number;
    description: string;
}

export interface DashboardAlert {
    title: string;
    description: string;
    href: string;
}
