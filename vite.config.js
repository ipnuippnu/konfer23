import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/scanner.js', 'resources/js/qr.js'],
            refresh: true,
        }),
    ]
});
