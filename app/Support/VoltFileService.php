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

            if (str_contains($relative, 'design-editor-preview') || str_contains($relative, '_editor-previews/')) {
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
     * Build a map of relative page path → website types derived from the navigation config.
     * Pages that appear in no navigation type return an empty array (treated as universal).
     *
     * @return array<string, list<string>>
     */
    public function buildPageTypeMap(): array
    {
        $navigation = config('navigation', []);
        $routeTypes = [];

        foreach ($navigation as $type => $config) {
            $navRoutes = array_column($config['nav'] ?? [], 'route');
            $footerRoutes = array_column($config['footer_company'] ?? [], 'route');

            foreach (array_unique(array_merge($navRoutes, $footerRoutes)) as $route) {
                $routeTypes[$route][] = $type;
            }
        }

        $typeMap = [];

        foreach ($this->listVoltFiles() as $group) {
            foreach ($group as $relativePath) {
                $routeName = $this->routeNameFromPath($relativePath);
                $typeMap[$relativePath] = $routeName ? ($routeTypes[$routeName] ?? []) : [];
            }
        }

        return $typeMap;
    }

    /**
     * Derive a route name from a relative page path.
     * e.g. "pages/⚡donate.blade.php" → "donate"
     * e.g. "pages/blog/⚡index.blade.php" → "blog.index"
     */
    private function routeNameFromPath(string $relativePath): ?string
    {
        $path = str_replace(['pages/', '⚡', '.blade.php'], '', $relativePath);
        $path = str_replace('/', '.', trim($path, '/'));

        return $path ?: null;
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
     * Generate a unique token for a user+page preview, scoped to the authenticated user and file.
     */
    public function previewToken(string $relativePath): string
    {
        return (string) auth()->id().'-'.md5($relativePath);
    }

    /**
     * The path for the live-preview temp file, scoped to the current user and page (gitignored).
     */
    public function previewFilePath(string $relativePath): string
    {
        $dir = resource_path('views/pages/_editor-previews');

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir.'/'.$this->previewToken($relativePath).'.blade.php';
    }

    /**
     * Write the current editor state to the preview temp file without touching the real file.
     *
     * @param  list<array{slug: string, name: string, blade: string}>  $rows
     */
    public function writePreviewFile(string $phpSection, array $rows, string $relativePath): void
    {
        $this->writeFile($this->previewFilePath($relativePath), $this->buildFileContent($phpSection, $rows));
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
            "new #[Layout('layouts.public')] #[Title('{$name}')] class extends Component {",
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
     * Clone an existing page to a new slug, copying its content and registering a new route.
     * Also adds the new page to the current website type's nav and footer as inactive.
     */
    public function clonePage(string $newSlug, string $newName, string $sourceRelativePath): void
    {
        $sourcePath = resource_path('views/'.$sourceRelativePath);
        $destPath = resource_path('views/pages/⚡'.$newSlug.'.blade.php');

        $contents = file_get_contents($sourcePath);

        $contents = preg_replace(
            "/#\[Title\('([^']*)'\)\]/",
            "#[Title('{$newName}')]",
            $contents
        );

        $this->writeFile($destPath, $contents);
        $this->addPublicRoute($newSlug);
    }

    /**
     * Rename a top-level public page file to a new slug and return the new relative path.
     * Route management is the responsibility of the caller.
     */
    public function renamePage(string $oldRelativePath, string $newSlug): string
    {
        $oldFullPath = resource_path('views/'.$oldRelativePath);
        $newRelativePath = 'pages/⚡'.$newSlug.'.blade.php';
        $newFullPath = resource_path('views/'.$newRelativePath);

        rename($oldFullPath, $newFullPath);

        return $newRelativePath;
    }

    /**
     * Delete a page file and remove its route from routes/web.php.
     */
    public function deletePage(string $relativePath): void
    {
        $fullPath = resource_path('views/'.$relativePath);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $routeName = $this->routeNameFromPath($relativePath);

        if ($routeName) {
            $this->removePageRoute($routeName);
        }
    }

    /**
     * Remove any Route::livewire entry for the given slug from routes/web.php,
     * regardless of which section or middleware chain it belongs to.
     */
    public function removePageRoute(string $slug): void
    {
        $routesPath = base_path('routes/web.php');
        $contents = file_get_contents($routesPath);
        $escapedSlug = preg_quote($slug, '/');

        $contents = preg_replace(
            '/\n[ \t]*Route::livewire\(\''.$escapedSlug.'\'[^\n]+;/',
            '',
            $contents
        );

        file_put_contents($routesPath, $contents);
    }

    /**
     * Add a route to the auth middleware group in routes/web.php.
     * Cached auth routes go inside the nested CacheResponse sub-group; uncached go outside it.
     * An optional role middleware is chained when a role restriction is required.
     */
    public function addAuthRoute(string $slug, bool $cached, string $role = ''): void
    {
        $routesPath = base_path('routes/web.php');
        $contents = file_get_contents($routesPath);
        $roleChain = $role ? "->middleware('role:{$role}')" : '';

        if ($cached) {
            $routeLine = "        Route::livewire('{$slug}', 'pages::{$slug}')->name('{$slug}'){$roleChain};";

            $contents = preg_replace(
                '/^(        \/\/ new auth-cached pages are inserted here)$/m',
                "$1\n{$routeLine}",
                $contents,
                1
            );
        } else {
            $routeLine = "    Route::livewire('{$slug}', 'pages::{$slug}')->name('{$slug}'){$roleChain};";

            $contents = preg_replace(
                '/^(    \/\/ new auth-uncached pages are inserted here)$/m',
                "$1\n{$routeLine}",
                $contents,
                1
            );
        }

        file_put_contents($routesPath, $contents);
    }

    /**
     * Determine whether a slug has an auth-protected route registered.
     */
    public function isAuthRoute(string $slug): bool
    {
        $routesPath = base_path('routes/web.php');
        $contents = file_get_contents($routesPath);

        $authSectionStart = strpos($contents, '// new auth-cached pages are inserted here');

        if ($authSectionStart === false) {
            return false;
        }

        $authSection = substr($contents, $authSectionStart);
        $escapedSlug = preg_quote($slug, '/');

        return (bool) preg_match("/Route::livewire\('{$escapedSlug}',/", $authSection);
    }

    /**
     * Determine whether an auth route is in the cached sub-group.
     * Returns true if cached (or if the anchors cannot be found).
     */
    public function isAuthRouteCached(string $slug): bool
    {
        $routesPath = base_path('routes/web.php');
        $contents = file_get_contents($routesPath);

        $cachedPos = strpos($contents, '// new auth-cached pages are inserted here');
        $uncachedPos = strpos($contents, '// new auth-uncached pages are inserted here');

        if ($cachedPos === false || $uncachedPos === false) {
            return true;
        }

        $cachedSection = substr($contents, $cachedPos, $uncachedPos - $cachedPos);
        $escapedSlug = preg_quote($slug, '/');

        return (bool) preg_match("/Route::livewire\('{$escapedSlug}',/", $cachedSection);
    }

    /**
     * Return the required role from an auth route's chained middleware, or '' if none.
     */
    public function getRouteAuthRole(string $slug): string
    {
        $routesPath = base_path('routes/web.php');
        $contents = file_get_contents($routesPath);
        $escapedSlug = preg_quote($slug, '/');

        if (preg_match("/Route::livewire\('{$escapedSlug}'[^;\n]+->middleware\('role:([^']+)'\)/", $contents, $m)) {
            return $m[1];
        }

        return '';
    }

    /**
     * Remove a route line from routes/web.php by its route name.
     * Handles both indented (cached group) and top-level (uncached) routes.
     */
    public function removePublicRoute(string $routeName): void
    {
        $routesPath = base_path('routes/web.php');
        $contents = file_get_contents($routesPath);
        $escapedName = preg_quote($routeName, '/');

        $contents = preg_replace(
            '/\n[ \t]*Route::livewire\([^\n]+->name\(\''.$escapedName.'\'\);/',
            '',
            $contents
        );

        file_put_contents($routesPath, $contents);
    }

    /**
     * Insert a new route into routes/web.php after the appropriate anchor comment.
     * Cached pages go inside the cache middleware group; uncached pages go outside it.
     */
    public function addPublicRoute(string $slug, bool $cached = true): void
    {
        $routesPath = base_path('routes/web.php');
        $contents = file_get_contents($routesPath);

        if ($cached) {
            $routeLine = "    Route::livewire('{$slug}', 'pages::{$slug}')->name('{$slug}');";

            $contents = preg_replace(
                '/^(    \/\/ new cached pages are inserted here)$/m',
                "$1\n    {$routeLine}",
                $contents,
                1
            );
        } else {
            $routeLine = "Route::livewire('{$slug}', 'pages::{$slug}')->name('{$slug}');";

            $contents = preg_replace(
                '/^(\/\/ new uncached pages are inserted here)$/m',
                "$1\n{$routeLine}",
                $contents,
                1
            );
        }

        file_put_contents($routesPath, $contents);
    }

    /**
     * Insert a redirect route into routes/web.php after the uncached pages anchor.
     * Placed outside the cache group so it is always evaluated.
     */
    public function addRedirectRoute(string $fromSlug, string $toSlug, int $status = 301): void
    {
        $routesPath = base_path('routes/web.php');
        $contents = file_get_contents($routesPath);
        $routeLine = "Route::redirect('{$fromSlug}', '/{$toSlug}', {$status});";

        $contents = preg_replace(
            '/^(\/\/ new uncached pages are inserted here)$/m',
            "$1\n{$routeLine}",
            $contents,
            1
        );

        file_put_contents($routesPath, $contents);
    }

    /**
     * Parse all Route::redirect() entries from routes/web.php.
     *
     * @return array<int, array{from: string, to: string, status: int}>
     */
    public function getRedirects(): array
    {
        $contents = file_get_contents(base_path('routes/web.php'));

        preg_match_all(
            "/Route::redirect\('([^']+)',\s*'([^']+)',\s*(\d+)\);/",
            $contents,
            $matches,
            PREG_SET_ORDER
        );

        return collect($matches)
            ->map(fn (array $m) => [
                'from' => $m[1],
                'to' => $m[2],
                'status' => (int) $m[3],
            ])
            ->values()
            ->all();
    }

    /**
     * Add a redirect route directly with raw from/to paths.
     */
    public function createRedirect(string $fromPath, string $toUrl, int $status = 301): void
    {
        $routesPath = base_path('routes/web.php');
        $contents = file_get_contents($routesPath);
        $routeLine = "Route::redirect('{$fromPath}', '{$toUrl}', {$status});";

        $contents = preg_replace(
            '/^(\/\/ new uncached pages are inserted here)$/m',
            "$1\n{$routeLine}",
            $contents,
            1
        );

        file_put_contents($routesPath, $contents);
    }

    /**
     * Remove a redirect route from routes/web.php by its from path.
     */
    public function removeRedirect(string $fromPath): void
    {
        $routesPath = base_path('routes/web.php');
        $contents = file_get_contents($routesPath);
        $escaped = preg_quote($fromPath, '/');

        $contents = preg_replace(
            "/\nRoute::redirect\('{$escaped}',\s*'[^']+',\s*\d+\);/",
            '',
            $contents
        );

        file_put_contents($routesPath, $contents);
    }

    /**
     * Update an existing redirect route in routes/web.php.
     */
    public function updateRedirect(string $originalFromPath, string $newFromPath, string $toUrl, int $status): void
    {
        $this->removeRedirect($originalFromPath);
        $this->createRedirect($newFromPath, $toUrl, $status);
    }

    /**
     * Determine whether a public page's route is inside the cache middleware group.
     * Returns true if cached (or if the route/anchor cannot be found).
     */
    public function isRouteCached(string $slug): bool
    {
        $routesPath = base_path('routes/web.php');
        $contents = file_get_contents($routesPath);
        $escapedSlug = preg_quote($slug, '/');

        // If route is not registered at all, default to cached.
        if (! preg_match("/Route::livewire\('{$escapedSlug}',/", $contents)) {
            return true;
        }

        $uncachedAnchorPos = strpos($contents, '// new uncached pages are inserted here');

        if ($uncachedAnchorPos === false) {
            return true;
        }

        $beforeUncached = substr($contents, 0, $uncachedAnchorPos);

        return (bool) preg_match("/Route::livewire\('{$escapedSlug}',/", $beforeUncached);
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
