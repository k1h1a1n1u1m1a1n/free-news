import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import autoprefixer from 'autoprefixer';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/scss/mobile.scss',
                'resources/scss/search.scss',
                'resources/js/app.js',
                'resources/js/search.js',
            ],
            refresh: true,
        }),
    ],
    css: {
        postcss: {
            plugins: [
                autoprefixer(),
            ],
        },
    },
});
