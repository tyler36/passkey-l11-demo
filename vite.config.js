import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const port = 5173;
const origin = `${process.env.DDEV_PRIMARY_URL}:${port}`;

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],

    // Adjust Vites dev server for DDEV: https://vitejs.dev/config/server-options.html
    server: {
        host: '0.0.0.0',
        port: port,
        origin: origin,
        strictPort: true
    },
});
