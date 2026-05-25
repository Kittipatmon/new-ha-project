import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // server: {
    //     host: '0.0.0.0', // เปิดให้คนอื่นเข้า
    //     hmr: {
    //         host: '172.17.23.222' // ระบุ IP ของเครื่องแม่ให้ชัดเจน
    //     },
    // },
});
