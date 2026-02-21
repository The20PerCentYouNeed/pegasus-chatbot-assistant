import { configure, initSession, sendMessage, fetchMessages } from "./api.js";
import { processStream } from "./sse.js";
import { getSession, saveSession } from "./storage.js";
import {
    mount,
    appendMessage,
    showTypingIndicator,
    appendStreamingMessage,
    appendToStreaming,
    finalizeStreaming,
    setInputEnabled,
    showError,
} from "./ui.js";
import "./styles.css";

(function () {
    const scriptTag =
        document.currentScript ||
        document.querySelector("script[data-pacman-api]");
    const apiUrl =
        scriptTag?.getAttribute("data-pacman-api") || window.location.origin;

    configure(apiUrl);

    document.addEventListener("DOMContentLoaded", () => {
        const wrapper = document.createElement("div");
        wrapper.id = "pacman-chat-widget";
        document.body.appendChild(wrapper);

        const ui = mount(wrapper);
        let isProcessing = false;
        let historyLoaded = false;

        async function ensureSession() {
            let session = getSession();
            if (session?.token) return session;

            try {
                const data = await initSession();
                session = { token: data.token, userId: data.user_id };
                saveSession(session);
                return session;
            } catch (err) {
                showError(
                    wrapper,
                    "Αποτυχία σύνδεσης. Δοκιμάστε ξανά αργότερα.",
                );
                throw err;
            }
        }

        async function loadHistory() {
            if (historyLoaded) return;
            historyLoaded = true;

            const session = getSession();
            if (!session?.token) return;

            try {
                const messages = await fetchMessages(session.token);
                messages.forEach((msg) => {
                    if (msg.role === "user" || msg.role === "assistant") {
                        appendMessage(wrapper, msg.role, msg.content);
                    }
                });
            } catch (err) {
                console.error("[PacMan Chat] Failed to load history", err);
            }
        }

        async function handleSend() {
            const input = ui.getInput();
            const message = input.value.trim();
            if (!message || isProcessing) return;

            isProcessing = true;
            input.value = "";
            setInputEnabled(wrapper, false);

            appendMessage(wrapper, "user", message);

            try {
                const session = await ensureSession();
                showTypingIndicator(wrapper);
                const response = await sendMessage(session.token, message);
                const streamEl = appendStreamingMessage(wrapper);

                await processStream(response, {
                    onToken(token) {
                        appendToStreaming(streamEl, token);
                    },
                    onDone() {
                        finalizeStreaming(streamEl);
                    },
                    onError(err) {
                        finalizeStreaming(streamEl);
                        showError(wrapper, "Σφάλμα κατά τη λήψη απάντησης.");
                        console.error("[PacMan Chat]", err);
                    },
                });
            } catch (err) {
                showError(wrapper, "Σφάλμα κατά την αποστολή μηνύματος.");
                console.error("[PacMan Chat]", err);
            } finally {
                isProcessing = false;
                setInputEnabled(wrapper, true);
                ui.getInput().focus();
            }
        }

        ui.onOpen(() => loadHistory());

        ui.getSendBtn().addEventListener("click", handleSend);

        ui.getInput().addEventListener("keydown", (e) => {
            if (e.key === "Enter" && !e.shiftKey) {
                e.preventDefault();
                handleSend();
            }
        });

        ui.getInput().addEventListener("input", function () {
            this.style.height = "auto";
            this.style.height = Math.min(this.scrollHeight, 100) + "px";
        });
    });
})();
