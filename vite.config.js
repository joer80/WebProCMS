import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
        watch: {
            ignored: [
                '**/resources/views/pages/_editor-previews/**',

                //Check these also
                '**/config/navigation.php',
                //'**/routes/web.php',
            ],
        },
    },
});
