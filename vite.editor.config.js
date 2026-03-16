import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import fs from 'fs';
import path from 'path';

/**
 * Merges the new partial manifest with the existing full manifest so that
 * entries for app.css, public.css etc. are not lost when only
 * the editor bundle is rebuilt.
 */
function mergeManifestPlugin() {
    let existingManifest = {};
    const manifestPath = path.resolve('public/build/manifest.json');

    return {
        name: 'merge-manifest',
        buildStart() {
            if (fs.existsSync(manifestPath)) {
                existingManifest = JSON.parse(fs.readFileSync(manifestPath, 'utf-8'));
            }
        },
        closeBundle() {
            if (fs.existsSync(manifestPath)) {
                const newManifest = JSON.parse(fs.readFileSync(manifestPath, 'utf-8'));
                const merged = { ...existingManifest, ...newManifest };
                fs.writeFileSync(manifestPath, JSON.stringify(merged, null, 2));
            }
        },
    };
}

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/editor.css'],
            refresh: false,
        }),
        tailwindcss(),
        mergeManifestPlugin(),
    ],
    build: {
        emptyOutDir: false,
    },
});
