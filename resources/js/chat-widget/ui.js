export function mount(container) {
    container.innerHTML = `
        <div class="pcm-bubble" aria-label="Open chat">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/>
            </svg>
        </div>
        <div class="pcm-panel pcm-hidden">
            <div class="pcm-header">
                <span class="pcm-header-title">Pack-Man Support</span>
                <button class="pcm-close" aria-label="Close chat">&times;</button>
            </div>
            <div class="pcm-messages"></div>
            <div class="pcm-input-area">
                <textarea class="pcm-input" placeholder="Γράψε το μήνυμά σου..." rows="1"></textarea>
                <button class="pcm-send" aria-label="Send message">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </div>
        </div>
    `;

    const bubble = container.querySelector(".pcm-bubble");
    const panel = container.querySelector(".pcm-panel");
    const closeBtn = container.querySelector(".pcm-close");

    let onOpenCallback = null;

    bubble.addEventListener("click", () => {
        panel.classList.remove("pcm-hidden");
        bubble.classList.add("pcm-hidden");
        container.querySelector(".pcm-input").focus();
        if (onOpenCallback) onOpenCallback();
    });

    closeBtn.addEventListener("click", () => {
        panel.classList.add("pcm-hidden");
        bubble.classList.remove("pcm-hidden");
    });

    return {
        getInput: () => container.querySelector(".pcm-input"),
        getSendBtn: () => container.querySelector(".pcm-send"),
        getMessagesContainer: () => container.querySelector(".pcm-messages"),
        onOpen(cb) { onOpenCallback = cb; },
    };
}

export function appendMessage(container, role, content) {
    const messagesEl = container.querySelector(".pcm-messages");
    const msgEl = document.createElement("div");
    msgEl.classList.add("pcm-msg", `pcm-msg-${role}`);
    msgEl.textContent = content;
    messagesEl.appendChild(msgEl);
    messagesEl.scrollTop = messagesEl.scrollHeight;
    return msgEl;
}

export function showTypingIndicator(container) {
    const messagesEl = container.querySelector(".pcm-messages");
    const el = document.createElement("div");
    el.classList.add("pcm-msg", "pcm-msg-assistant", "pcm-typing");
    el.innerHTML = '<span class="pcm-dot"></span><span class="pcm-dot"></span><span class="pcm-dot"></span>';
    messagesEl.appendChild(el);
    messagesEl.scrollTop = messagesEl.scrollHeight;
    return el;
}

export function removeTypingIndicator(container) {
    const el = container.querySelector(".pcm-typing");
    if (el) el.remove();
}

export function appendStreamingMessage(container) {
    removeTypingIndicator(container);
    const messagesEl = container.querySelector(".pcm-messages");
    const msgEl = document.createElement("div");
    msgEl.classList.add("pcm-msg", "pcm-msg-assistant", "pcm-streaming");
    messagesEl.appendChild(msgEl);
    messagesEl.scrollTop = messagesEl.scrollHeight;
    return msgEl;
}

export function appendToStreaming(msgEl, token) {
    msgEl.textContent += token;
    const messagesEl = msgEl.parentElement;
    if (messagesEl) {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }
}

export function finalizeStreaming(msgEl) {
    msgEl.classList.remove("pcm-streaming");
}

export function setInputEnabled(container, enabled) {
    const input = container.querySelector(".pcm-input");
    const btn = container.querySelector(".pcm-send");
    input.disabled = !enabled;
    btn.disabled = !enabled;
}

export function showError(container, message) {
    appendMessage(container, "error", message);
}
