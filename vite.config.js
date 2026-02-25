import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/public.css', 'resources/css/editor.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
        watch: {
            ignored: [
                //Don't watch the design editor preview files, we only need to refresh them when we save in the editor. It causes flickering when editing.
                '**/resources/views/pages/_editor-previews/**',

                //Doesnt need hot reload. We can not see the front end menu while editing anyway, and it refreshes the page messing with the toast message.
                '**/config/navigation.php',
            ],
        },
    },
});
