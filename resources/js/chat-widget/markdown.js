import MarkdownIt from "markdown-it";
import DOMPurify from "dompurify";

const markdown = new MarkdownIt({
    html: false,
    linkify: true,
    breaks: true,
    typographer: false,
});

function applySafeLinkAttributes(sanitizedHtml) {
    const template = document.createElement("template");
    template.innerHTML = sanitizedHtml;

    template.content.querySelectorAll("a").forEach((anchor) => {
        anchor.setAttribute("target", "_blank");
        anchor.setAttribute("rel", "noopener noreferrer");
    });

    return template.innerHTML;
}

export function renderMarkdown(content = "") {
    const rendered = markdown.render(String(content ?? ""));
    const sanitized = DOMPurify.sanitize(rendered, {
        USE_PROFILES: { html: true },
    });

    return applySafeLinkAttributes(sanitized);
}
