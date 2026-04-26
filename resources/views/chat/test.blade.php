<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pegasus Chat Widget — Test</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 40px;
            background: #f9fafb;
            color: #1f2937;
        }
        h1 { font-size: 24px; margin-bottom: 8px; }
        p { color: #6b7280; }
    </style>
</head>
<body>
    <h1>Pegasus Chat Widget Test</h1>
    <p>The chat widget should appear in the bottom-right corner.</p>

    @vite('resources/js/chat-widget/index.js')
</body>
</html>
