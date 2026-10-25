import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/admin/login-entry.css',
                'resources/js/app.js',
                'resources/js/login-entry.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
