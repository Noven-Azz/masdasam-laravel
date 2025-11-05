import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    server: {
        port: 5174, // ganti port disini (contoh 5174)
        host: true, // atau '0.0.0.0' untuk expose ke jaringan
        // hmr: { host: 'localhost' } // optional bila butuh HMR config custom
    },
});
