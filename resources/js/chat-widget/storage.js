const STORAGE_KEY = 'pcm_chat';
const TTL_MS = 24 * 60 * 60 * 1000;

export function getSession() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return null;

        const session = JSON.parse(raw);

        if (Date.now() - session.createdAt > TTL_MS) {
            clearSession();
            return null;
        }

        return session;
    } catch {
        return null;
    }
}

export function saveSession(data) {
    try {
        data.createdAt = Date.now();
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    } catch {
        // localStorage unavailable (private browsing, etc.)
    }
}

export function clearSession() {
    try {
        localStorage.removeItem(STORAGE_KEY);
    } catch {
        // noop
    }
}

export function getToken() {
    const session = getSession();
    return session?.token ?? null;
}
