let baseUrl = "";

export function configure(url) {
    baseUrl = url.replace(/\/$/, "");
}

export async function initSession() {
    const res = await fetch(`${baseUrl}/api/chat/sessions`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
        },
    });

    if (!res.ok) {
        throw new Error(`Session init failed: ${res.status}`);
    }

    return res.json();
}

const WELCOME_MESSAGE = {
    role: "assistant",
    content: "Γεια σου! 👋 Είμαι ο AI Assistant της Pack-Man Courier. Ρώτα με ό,τι θέλεις σχετικά με αποστολές, υπηρεσίες ή οτιδήποτε άλλο — είμαι εδώ να βοηθήσω!",
};

export async function fetchMessages(token) {
    const res = await fetch(`${baseUrl}/api/chat/messages`, {
        headers: {
            Accept: "application/json",
            Authorization: `Bearer ${token}`,
        },
    });

    if (!res.ok) {
        throw new Error(`Fetch messages failed: ${res.status}`);
    }

    const messages = await res.json();
    return [WELCOME_MESSAGE, ...messages];
}

export async function sendMessage(token, message) {
    const res = await fetch(`${baseUrl}/api/chat/messages`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "text/event-stream",
            Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ message }),
    });

    if (!res.ok) {
        throw new Error(`Send message failed: ${res.status}`);
    }

    return res;
}
