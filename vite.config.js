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

                //Written at runtime by the settings page dashboard. Same reason as navigation.php.
                '**/config/business.php',
                '**/config/seo.php',
                '**/config/branding.php',
                '**/config/layout.php',

                //Top level Blade files are written at runtime by the editor. The editor handles its own preview refresh, so Vite watching these causes a full page reload that wipes toast notifications. Subdirectories like dashboard still hot reload in vite.
                '**/resources/views/pages/⚡*.blade.php',

                //Shared row files are written at runtime when a row is made shared. No need to trigger HMR.
                '**/resources/views/shared-rows/**',
            ],
        },
    },
});
