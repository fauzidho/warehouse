import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
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
        chunkSizeWarningLimit: 800,
        rollupOptions: {
            output: {
                manualChunks: {
                    // Pisahkan Firebase SDK ke chunk terpisah
                    // sehingga tidak dimuat pada halaman yang tidak membutuhkannya
                    'firebase-vendor': [
                        'firebase/app',
                        'firebase/firestore',
                        'firebase/database',
                    ],
                },
            },
        },
    },
});
