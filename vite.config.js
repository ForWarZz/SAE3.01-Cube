import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    base: '/~s315-cube/',

    plugins: [
        tailwindcss(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/overlay360/main.js',
                'resources/js/bikesize/main.js',
                'resources/js/help/main.js',
            ],
            refresh: true,
        }),
    ],
});
