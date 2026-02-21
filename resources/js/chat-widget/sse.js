/**
 * Process an SSE response stream from the agent.
 *
 * @param {Response} response - Fetch response with text/event-stream body
 * @param {object} callbacks
 * @param {function(string): void} callbacks.onToken - Called for each text chunk
 * @param {function(): void} callbacks.onDone - Called when stream ends
 * @param {function(Error): void} callbacks.onError - Called on error
 */
export async function processStream(response, { onToken, onDone, onError }) {
    try {
        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        let buffer = '';

        while (true) {
            const { done, value } = await reader.read();
            if (done) break;

            buffer += decoder.decode(value, { stream: true });
            const lines = buffer.split('\n');
            buffer = lines.pop() ?? '';

            for (const line of lines) {
                if (line.startsWith('data: ')) {
                    const data = line.slice(6).trim();
                    if (data === '[DONE]') {
                        onDone();
                        return;
                    }

                    try {
                        const parsed = JSON.parse(data);

                        if (parsed.type === 'text_delta' && parsed.delta) {
                            // TODO: remove this delay — only for testing the streaming effect
                            await new Promise(r => setTimeout(r, 50));
                            onToken(parsed.delta);
                        }
                    } catch {
                        if (data) {
                            onToken(data);
                        }
                    }
                }
            }
        }

        onDone();
    } catch (err) {
        onError(err);
    }
}
