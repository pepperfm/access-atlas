export function userStatusLabel(status: string): string {
    const labels: Record<string, string> = {
        active: 'Активен',
        inactive: 'Неактивен',
        suspended: 'Заблокирован',
    };

    return labels[status] ?? status;
}

export function appRoleLabel(role: string | null): string {
    const labels: Record<string, string> = {
        owner: 'Владелец',
        tech_lead: 'Техлид',
        manager: 'Менеджер',
        developer: 'Разработчик',
    };

    return role ? (labels[role] ?? role) : 'Не назначена';
}

export function projectRoleLabel(role: string): string {
    const labels: Record<string, string> = {
        owner: 'Владелец',
        tech_lead: 'Техлид',
        manager: 'Менеджер',
        developer: 'Разработчик',
    };

    return labels[role] ?? role;
}

export function projectStatusLabel(status: string): string {
    const labels: Record<string, string> = {
        active: 'Активен',
        archived: 'Архивирован',
        draft: 'Черновик',
    };

    return labels[status] ?? status;
}

export function criticalityLabel(value: string): string {
    const labels: Record<string, string> = {
        low: 'Низкая',
        medium: 'Средняя',
        high: 'Высокая',
        critical: 'Критическая',
    };

    return labels[value] ?? value;
}

export function sensitivityLabel(value: string): string {
    const labels: Record<string, string> = {
        low: 'Низкая',
        medium: 'Средняя',
        high: 'Высокая',
        critical: 'Критическая',
    };

    return labels[value] ?? value;
}

export function accessLevelLabel(value: string): string {
    const labels: Record<string, string> = {
        read: 'Только чтение',
        write: 'Изменение',
        admin: 'Админ',
        owner: 'Владелец',
    };

    return labels[value] ?? value;
}

export function grantKindLabel(value: string): string {
    const labels: Record<string, string> = {
        credential: 'Логин и пароль',
        token: 'Токен',
        role: 'Роль',
        ownership: 'Владелец',
    };

    return labels[value] ?? value;
}

export function secretStorageModeLabel(value: string): string {
    const labels: Record<string, string> = {
        encrypted_value: 'Зашифрованное значение',
        external_reference: 'Внешний reference',
    };

    return labels[value] ?? value;
}
