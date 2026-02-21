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

    return res.json();
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
