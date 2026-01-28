import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/barbery.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: 'localhost',
        port: 5174,
        strictPort: true,
    },
});
