import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

const port = 5173;
const origin = `${process.env.DDEV_PRIMARY_URL}:${port}`;

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],

    // Adjust Vites dev server for DDEV: https://vitejs.dev/config/server-options.html
    server: {
        // The following line is require until the release of https://github.com/vitejs/vite/pull/19241
        cors: { origin: process.env.DDEV_PRIMARY_URL },
        // ----------------
        host: '0.0.0.0',
        port: port,
        origin: origin,
        strictPort: true
    },
});
