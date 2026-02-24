<?php

namespace App\Support;

use Illuminate\Support\Str;

class VoltFileService
{
    /**
     * List all volt .blade.php files under resources/views/pages/, grouped by section.
     *
     * @return array<string, array<string, string>> section => [label => relativePath]
     */
    public function listVoltFiles(): array
    {
        $pagesDir = resource_path('views/pages');
        $groups = ['Public Pages' => [], 'Dashboard' => [], 'Other' => []];

        $topLevel = glob($pagesDir.'/*.blade.php') ?: [];
        $nested = glob($pagesDir.'/**/*.blade.php') ?: [];
        $files = array_merge($topLevel, $nested);

        foreach ($files as $fullPath) {
            $relative = str_replace(resource_path('views').'/', '', $fullPath);
            $label = $this->labelFromPath($relative);

            if (str_contains($relative, 'pages/auth/')) {
                continue;
            }

            if (str_contains($relative, 'pages/dashboard/')) {
                $groups['Dashboard'][$label] = $relative;
            } elseif (str_contains($relative, 'pages/settings/')) {
                $groups['Other'][$label] = $relative;
            } else {
                $groups['Public Pages'][$label] = $relative;
            }
        }

        return array_filter($groups);
    }

    /**
     * Parse a volt file into its PHP class section and blade rows.
     *
     * @return array{phpSection: string, rows: list<array{slug: string, name: string, blade: string}>}
     */
    public function parseFile(string $fullPath): array
    {
        $contents = file_get_contents($fullPath);
        $phpEnd = strpos($contents, '?>');

        if ($phpEnd === false) {
            $legacySlug = 'legacy-'.Str::random(6);

            return [
                'phpSection' => '',
                'rows' => [['slug' => $legacySlug, 'name' => 'Existing Content', 'blade' => trim($contents)]],
            ];
        }

        $phpSection = substr($contents, 0, $phpEnd + 2);
        $bladeSection = trim(substr($contents, $phpEnd + 2));

        // Strip the outer <div> wrapper added by buildFileContent
        if (preg_match('/^\s*<div>(.*)<\/div>\s*$/s', $bladeSection, $m)) {
            $bladeSection = trim($m[1]);
        }

        $rows = $this->parseBladeRows($bladeSection);

        return [
            'phpSection' => $phpSection,
            'rows' => $rows,
        ];
    }

    /**
     * Reconstruct a volt file's content from its PHP section and rows array.
     *
     * @param  list<array{slug: string, name: string, blade: string}>  $rows
     */
    public function buildFileContent(string $phpSection, array $rows): string
    {
        $blade = '';

        foreach ($rows as $row) {
            $slug = $row['slug'];
            $blade .= "\n{{-- ROW:start:{$slug} --}}\n";
            $blade .= $row['blade'];
            $blade .= "\n{{-- ROW:end:{$slug} --}}\n";
        }

        return $phpSection."\n<div>".trim($blade)."\n</div>\n";
    }

    /**
     * Inject a PHP code block into the volt class section before the closing }; ?>.
     * Idempotent — will not re-inject if the slug is already present.
     */
    public function injectPhpCode(string $phpSection, string $phpCode, string $slug): string
    {
        $startMarker = '// ROW:php:start:'.$slug;

        if (str_contains($phpSection, $startMarker)) {
            return $phpSection;
        }

        $endMarker = '// ROW:php:end:'.$slug;
        $indented = implode("\n    ", explode("\n", trim($phpCode)));
        $block = "\n    {$startMarker}\n    {$indented}\n    {$endMarker}";

        return preg_replace('/(\};\s*\?>)/', $block."\n$1", $phpSection, 1);
    }

    /**
     * Remove a PHP code block from the volt class section by slug.
     */
    public function removePhpCode(string $phpSection, string $slug): string
    {
        $escapedSlug = preg_quote($slug, '/');
        $pattern = '/\n\s*\/\/ ROW:php:start:'.$escapedSlug.'.*?\/\/ ROW:php:end:'.$escapedSlug.'\n?/s';

        return preg_replace($pattern, '', $phpSection);
    }

    /**
     * Write content back to a volt file.
     */
    public function writeFile(string $fullPath, string $content): void
    {
        file_put_contents($fullPath, $content);
    }

    /**
     * The fixed path for the live-preview temp file (gitignored).
     */
    public function previewFilePath(): string
    {
        return resource_path('views/pages/design-editor-preview.blade.php');
    }

    /**
     * Write the current editor state to the preview temp file without touching the real file.
     */
    public function writePreviewFile(string $phpSection, array $rows): void
    {
        $this->writeFile($this->previewFilePath(), $this->buildFileContent($phpSection, $rows));
    }

    /**
     * Attempt to derive a public URL for a volt file's relative view path.
     */
    public function getRouteForFile(string $relativePath): string
    {
        $routeMap = $this->buildRouteMap();
        $viewId = $this->pathToViewId($relativePath);

        if (isset($routeMap[$viewId])) {
            try {
                return route($routeMap[$viewId]);
            } catch (\Exception) {
                // Route may require parameters — fall through to URL derivation
            }
        }

        return $this->deriveUrlFromPath($relativePath);
    }

    /**
     * Parse the blade section into rows using ROW:start/end comment markers.
     * Wraps any unmarked content in a legacy block.
     *
     * @return list<array{slug: string, name: string, blade: string}>
     */
    private function parseBladeRows(string $bladeSection): array
    {
        $rows = [];
        $startTag = 'ROW:start:';
        $endTag = 'ROW:end:';
        $pattern = '/\{\{--\s*'.$startTag.'([\w-]+)\s*--\}\}(.*?)\{\{--\s*'.$endTag.'\1\s*--\}\}/s';

        preg_match_all($pattern, $bladeSection, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        if (empty($matches)) {
            if (! empty(trim($bladeSection))) {
                $rows[] = [
                    'slug' => 'legacy-'.Str::random(6),
                    'name' => 'Existing Content',
                    'blade' => trim($bladeSection),
                ];
            }

            return $rows;
        }

        $lastEnd = 0;

        foreach ($matches as $match) {
            $fullMatchStart = $match[0][1];
            $fullMatchLen = strlen($match[0][0]);
            $slug = $match[1][0];
            $blade = trim($match[2][0]);

            $before = substr($bladeSection, $lastEnd, $fullMatchStart - $lastEnd);

            if (! empty(trim($before))) {
                $rows[] = [
                    'slug' => 'legacy-'.Str::random(6),
                    'name' => 'Existing Content',
                    'blade' => trim($before),
                ];
            }

            $rows[] = [
                'slug' => $slug,
                'name' => $this->labelFromSlug($slug),
                'blade' => $blade,
            ];

            $lastEnd = $fullMatchStart + $fullMatchLen;
        }

        $after = substr($bladeSection, $lastEnd);

        if (! empty(trim($after))) {
            $rows[] = [
                'slug' => 'legacy-'.Str::random(6),
                'name' => 'Existing Content',
                'blade' => trim($after),
            ];
        }

        return $rows;
    }

    /**
     * Build a map of Livewire view identifiers to route names from registered routes.
     *
     * @return array<string, string>
     */
    private function buildRouteMap(): array
    {
        $map = [];

        foreach (app('router')->getRoutes() as $route) {
            $action = $route->getAction();

            if (isset($action['view'])) {
                $viewId = str_replace('.', '::', $action['view']);
                $name = $route->getName();

                if ($name) {
                    $map[$viewId] = $name;
                }
            }
        }

        return $map;
    }

    /**
     * Convert a relative view path to a Livewire view identifier.
     * e.g. "pages/about.blade.php" => "pages::about"
     */
    private function pathToViewId(string $relativePath): string
    {
        $path = str_replace('⚡', '', $relativePath);
        $path = str_replace('.blade.php', '', $path);
        $path = str_replace('/', '.', $path);

        return str_replace('pages.', 'pages::', $path);
    }

    /**
     * Derive a URL from a volt file path as a fallback.
     */
    private function deriveUrlFromPath(string $relativePath): string
    {
        $path = str_replace(['pages/', '⚡', '.blade.php'], '', $relativePath);
        $path = rtrim($path, '/');

        if ($path === 'dashboard' || $path === 'pages') {
            return url('/dashboard');
        }

        return url('/'.$path);
    }

    /**
     * Generate a human-readable label from a file path.
     */
    private function labelFromPath(string $relativePath): string
    {
        $name = basename($relativePath, '.blade.php');
        $name = str_replace('⚡', '', $name);

        return ucwords(str_replace(['-', '_'], ' ', $name));
    }

    /**
     * Create a new blank public Volt page file and register its route.
     */
    public function createPage(string $slug, string $name): void
    {
        $fullPath = resource_path('views/pages/⚡'.$slug.'.blade.php');

        $content = implode("\n", [
            '<?php',
            '',
            'use Livewire\Attributes\Layout;',
            'use Livewire\Attributes\Title;',
            'use Livewire\Component;',
            '',
            "new #[Layout('layouts.app')] #[Title('{$name}')] class extends Component {",
            '}; ?>',
            '',
            '<div>',
            '</div>',
            '',
        ]);

        $this->writeFile($fullPath, $content);
        $this->addPublicRoute($slug);
    }

    /**
     * Insert a new route into the cache middleware group in routes/web.php.
     */
    public function addPublicRoute(string $slug): void
    {
        $routesPath = base_path('routes/web.php');
        $contents = file_get_contents($routesPath);
        $routeLine = "    Route::livewire('{$slug}', 'pages::{$slug}')->name('{$slug}');";

        $contents = preg_replace(
            '/(\}\);)(\n\nRoute::livewire\(\'contact\')/',
            $routeLine."\n$1$2",
            $contents
        );

        file_put_contents($routesPath, $contents);
    }

    /**
     * Generate a human-readable label from a row slug.
     * e.g. "hero-a1b2c3" => "Hero"
     */
    private function labelFromSlug(string $slug): string
    {
        $parts = explode('-', $slug);

        if (count($parts) > 1 && strlen(end($parts)) === 6) {
            array_pop($parts);
        }

        return ucwords(implode(' ', $parts));
    }
}
