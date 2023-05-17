import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/play-vue.js',
                'resources/js/fight-vue.js',
                'public/js/fight.js',
            ],
            refresh: true,
        }),
    ],
});
