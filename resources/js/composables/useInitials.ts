export interface UseInitialsReturn {
    getInitials: (fullName?: string) => string;
}

export function getInitials(fullName?: string): string {
    if (!fullName) {
        return '';
    }

    const names = fullName.trim().split(' ');

    if (names.length === 0) {
        return '';
    }
    if (names.length === 1) {
        return names[0].charAt(0).toUpperCase();
    }

    const lastName = names.at(-1);

    if (!lastName) {
        return names[0].charAt(0).toUpperCase();
    }

    return `${names[0].charAt(0)}${lastName.charAt(0)}`.toUpperCase();
}

export function useInitials(): UseInitialsReturn {
    return { getInitials };
}
