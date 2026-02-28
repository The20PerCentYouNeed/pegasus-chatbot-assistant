const BOT_AVATAR_SVG = `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>`;

export function mount(container, options = {}) {
    const shouldShowTeaser = options.showTeaser ?? true;

    container.innerHTML = `
        <button class="pcm-teaser ${shouldShowTeaser ? "" : "pcm-teaser-hidden"}" aria-label="Open chat teaser" type="button">
            <span class="pcm-teaser-line pcm-teaser-line-top">Πώς μπορώ να σε</span>
            <span class="pcm-teaser-line pcm-teaser-line-bottom">βοηθήσω;</span>
        </button>
        <button class="pcm-bubble" aria-label="Open chat">
            <svg class="pcm-icon-chat" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <svg class="pcm-icon-close" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div class="pcm-panel pcm-hidden">
            <div class="pcm-header">
                <div class="pcm-header-left">
                    <div class="pcm-avatar">
                        <div class="pcm-avatar-icon">${BOT_AVATAR_SVG}</div>
                        <div class="pcm-avatar-status"></div>
                    </div>
                    <div>
                        <div class="pcm-header-title">Pack-Man AI Assistant</div>
                        <div class="pcm-header-subtitle">
                            <span class="pcm-online-dot"></span>
                            Online · AI-Powered
                        </div>
                    </div>
                </div>
                <button class="pcm-close" aria-label="Close chat">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="pcm-messages"></div>
            <div class="pcm-input-area">
                <div class="pcm-input-row">
                    <div class="pcm-input-wrapper">
                        <input type="text" class="pcm-input" placeholder="Γράψτε την ερώτησή σας..." />
                    </div>
                    <button class="pcm-send" disabled aria-label="Send message">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 21l21-9L2 3v7l15 2-15 2v7z"/>
                        </svg>
                    </button>
                </div>
                <div class="pcm-powered-by"><a href="https://www.noctuacore.ai" target="_blank" rel="noopener noreferrer">Powered by Noctuacore AI</a></div>
            </div>
        </div>
    `;

    const bubble = container.querySelector(".pcm-bubble");
    const teaser = container.querySelector(".pcm-teaser");
    const panel = container.querySelector(".pcm-panel");
    const closeBtn = container.querySelector(".pcm-close");

    let onOpenCallback = null;
    let teaserEnabled = shouldShowTeaser;

    function showTeaser() {
        if (!teaser || !teaserEnabled) return;
        teaser.classList.remove("pcm-teaser-hidden");
    }

    function hideTeaser() {
        if (!teaser) return;
        teaser.classList.add("pcm-teaser-hidden");
    }

    function openPanel() {
        panel.classList.remove("pcm-hidden");
        bubble.classList.add("pcm-open");
        hideTeaser();
        container.querySelector(".pcm-input").focus();
        if (onOpenCallback) onOpenCallback();
    }

    function closePanel() {
        panel.classList.add("pcm-hidden");
        bubble.classList.remove("pcm-open");
        showTeaser();
    }

    function disableTeaser() {
        teaserEnabled = false;
        hideTeaser();
    }

    bubble.addEventListener("click", () => {
        if (bubble.classList.contains("pcm-open")) {
            closePanel();
        } else {
            openPanel();
        }
    });

    teaser?.addEventListener("click", openPanel);

    closeBtn.addEventListener("click", closePanel);

    return {
        getInput: () => container.querySelector(".pcm-input"),
        getSendBtn: () => container.querySelector(".pcm-send"),
        getMessagesContainer: () => container.querySelector(".pcm-messages"),
        disableTeaser,
        onOpen(cb) {
            onOpenCallback = cb;
        },
    };
}

export function appendMessage(container, role, content) {
    const messagesEl = container.querySelector(".pcm-messages");
    const row = document.createElement("div");
    row.classList.add(
        "pcm-msg-row",
        role === "user" ? "pcm-msg-row-user" : "pcm-msg-row-bot",
    );

    if (role === "assistant") {
        const avatar = document.createElement("div");
        avatar.classList.add("pcm-bot-avatar");
        avatar.innerHTML = BOT_AVATAR_SVG;
        row.appendChild(avatar);
    }

    const bubble = document.createElement("div");
    bubble.classList.add("pcm-msg", `pcm-msg-${role}`);
    bubble.textContent = content;
    row.appendChild(bubble);

    messagesEl.appendChild(row);
    messagesEl.scrollTop = messagesEl.scrollHeight;
    return bubble;
}

export function showTypingIndicator(container) {
    const messagesEl = container.querySelector(".pcm-messages");
    const row = document.createElement("div");
    row.classList.add("pcm-msg-row", "pcm-msg-row-bot", "pcm-typing-row");

    const avatar = document.createElement("div");
    avatar.classList.add("pcm-bot-avatar");
    avatar.innerHTML = BOT_AVATAR_SVG;
    row.appendChild(avatar);

    const dots = document.createElement("div");
    dots.classList.add("pcm-typing-dots");
    dots.innerHTML =
        '<span class="pcm-dot"></span><span class="pcm-dot"></span><span class="pcm-dot"></span>';
    row.appendChild(dots);

    messagesEl.appendChild(row);
    messagesEl.scrollTop = messagesEl.scrollHeight;
    return row;
}

export function removeTypingIndicator(container) {
    const el = container.querySelector(".pcm-typing-row");
    if (el) el.remove();
}

export function appendStreamingMessage(container) {
    removeTypingIndicator(container);
    const messagesEl = container.querySelector(".pcm-messages");

    const row = document.createElement("div");
    row.classList.add("pcm-msg-row", "pcm-msg-row-bot");

    const avatar = document.createElement("div");
    avatar.classList.add("pcm-bot-avatar");
    avatar.innerHTML = BOT_AVATAR_SVG;
    row.appendChild(avatar);

    const bubble = document.createElement("div");
    bubble.classList.add("pcm-msg", "pcm-msg-assistant", "pcm-streaming");
    row.appendChild(bubble);

    messagesEl.appendChild(row);
    messagesEl.scrollTop = messagesEl.scrollHeight;
    return bubble;
}

export function appendToStreaming(msgEl, token) {
    msgEl.textContent += token;
    const messagesEl = msgEl.closest(".pcm-messages");
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
    btn.disabled = !enabled || !input.value.trim();
}

export function showError(container, message) {
    const messagesEl = container.querySelector(".pcm-messages");
    const msgEl = document.createElement("div");
    msgEl.classList.add("pcm-msg", "pcm-msg-error");
    msgEl.textContent = message;
    messagesEl.appendChild(msgEl);
    messagesEl.scrollTop = messagesEl.scrollHeight;
}
