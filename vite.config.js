import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/chat-widget/index.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    build: {
        rollupOptions: {
            output: {
                entryFileNames: (chunkInfo) => {
                    if (chunkInfo.name === 'index' || chunkInfo.facadeModuleId?.includes('chat-widget')) {
                        return 'pacman-chat-widget.js';
                    }
                    return 'assets/[name]-[hash].js';
                },
            },
        },
    },
});
