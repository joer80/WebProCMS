<?php

namespace Database\Seeders;

use App\Jobs\IndexDesignLibraryJob;
use App\Models\DesignPage;
use App\Models\DesignRow;
use App\Models\Setting;
use App\Support\VoltFileService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClientPageSeeder extends Seeder
{
    /**
     * Install client-editable page files from the design library if they don't already exist.
     * Safe to re-run — existing files are never overwritten.
     */
    public function run(): void
    {
        // Ensure design library is indexed before pulling templates from it.
        IndexDesignLibraryJob::dispatchSync();

        $pages = [
            [
                'dl_source' => 'pages/custom/home.blade.php',
                'dest' => 'pages/⚡home.blade.php',
                'layout' => 'layouts.public',
                'layout_params' => ['description' => 'Welcome to our website.'],
                'title' => 'Home',
                'route_line' => "    Route::livewire('/', 'pages::home')->name('home');",
                'route_check' => "'pages::home'",
            ],
            [
                'dl_source' => 'pages/custom/about.blade.php',
                'dest' => 'pages/⚡about.blade.php',
                'layout' => 'layouts.public',
                'title' => 'About',
                'route_line' => "    Route::livewire('about', 'pages::about')->name('about');",
                'route_check' => "'pages::about'",
            ],
            [
                'dl_source' => 'pages/custom/contact.blade.php',
                'dest' => 'pages/⚡contact.blade.php',
                'layout' => 'layouts.public',
                'title' => 'Contact',
                'route_line' => "    Route::livewire('contact', 'pages::contact')->name('contact');",
                'route_check' => "'pages::contact'",
            ],
            [
                'dl_source' => 'pages/custom/services.blade.php',
                'dest' => 'pages/⚡services.blade.php',
                'layout' => 'layouts.public',
                'title' => 'Services',
                'route_line' => "    Route::livewire('services', 'pages::services')->name('services');",
                'route_check' => "'pages::services'",
            ],
            [
                'dl_source' => 'pages/custom/locations.blade.php',
                'dest' => 'pages/⚡locations.blade.php',
                'layout' => 'layouts.public',
                'title' => 'Locations',
                'extra_php' => "public string \$pageName = 'Locations';",
                'route_line' => "    Route::livewire('locations', 'pages::locations')->name('locations');",
                'route_check' => "'pages::locations'",
            ],
            [
                'dl_source' => 'pages/custom/404.blade.php',
                'dest' => 'pages/⚡404.blade.php',
                'layout' => 'layouts.public',
                'layout_params' => ['status' => 'unlisted'],
                'title' => '404',
                'route_line' => "    Route::livewire('404', 'pages::404')->name('404');",
                'route_check' => "'pages::404'",
            ],
            [
                'dl_source' => 'pages/custom/blog-index.blade.php',
                'dest' => 'pages/blog/⚡index.blade.php',
                'layout' => 'layouts.public',
                'layout_params' => ['description' => 'Read our latest articles, insights, and updates.'],
                'title' => 'Blog',
                'extra_php' => "public string \$pageName = 'Blog';",
                'route_line' => "    Route::livewire('blog', 'pages::blog.index')->name('blog.index');",
                'route_check' => "'pages::blog.index'",
            ],
            [
                'dl_source' => 'pages/custom/blog-show.blade.php',
                'dest' => 'pages/blog/⚡show.blade.php',
                'layout' => 'layouts.public',
                // No title — the blog-post-article PHP block injects a dynamic title() method.
                'route_line' => "    Route::livewire('blog/{slug}', 'pages::blog.show')->name('blog.show');",
                'route_check' => "'pages::blog.show'",
            ],
        ];

        $voltService = new VoltFileService;

        foreach ($pages as $config) {
            $destPath = resource_path("views/{$config['dest']}");

            // Always ensure the route exists, even if the page file was already created.
            if (isset($config['route_line'], $config['route_check'])) {
                $this->addRouteIfMissing($config['route_line'], $config['route_check']);
            }

            if (file_exists($destPath)) {
                // Record hash for existing seeded pages not yet tracked (e.g. first run of updated seeder).
                $this->recordSeededPageIfMissing($destPath, $config['route_check'] ?? null);

                continue;
            }

            $destDir = dirname($destPath);

            if (! is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            $dlPage = DesignPage::query()
                ->where('source_file', $config['dl_source'])
                ->first();

            if (! $dlPage || empty($dlPage->row_names)) {
                continue;
            }

            $phpSection = $this->buildPhpSection($config);
            $rows = [];

            foreach ($dlPage->row_names as $templateName) {
                $dlRow = DesignRow::query()
                    ->where('source_file', 'like', '%/'.$templateName.'.blade.php')
                    ->first();

                if (! $dlRow) {
                    continue;
                }

                $slug = $templateName.':'.Str::random(6);
                $blade = str_replace('__SLUG__', $slug, $dlRow->bladeCodeFromFile());

                $rows[] = [
                    'slug' => $slug,
                    'name' => $dlRow->name,
                    'blade' => $blade,
                ];

                $phpCode = $dlRow->phpCodeFromFile();

                if ($phpCode) {
                    $phpSection = $voltService->injectPhpCode($phpSection, $phpCode, $slug);
                }
            }

            $content = $voltService->buildFileContent($phpSection, $rows);

            file_put_contents($destPath, $content);

            $this->recordSeededPage($destPath, $config['route_check'] ?? null);
        }
    }

    /**
     * Store the MD5 hash of a seeded page file so we can detect if it was later edited.
     */
    private function recordSeededPage(string $path, ?string $routeCheck): void
    {
        /** @var array<string, array{hash: string, route_check: string|null}> $pages */
        $pages = Setting::get('seeded_client_pages', []);
        $pages[$path] = [
            'hash' => md5_file($path),
            'route_check' => $routeCheck,
        ];
        Setting::set('seeded_client_pages', $pages);
    }

    /**
     * Record the hash only if this path has not been tracked yet (safe for re-runs).
     */
    private function recordSeededPageIfMissing(string $path, ?string $routeCheck): void
    {
        /** @var array<string, array{hash: string, route_check: string|null}> $pages */
        $pages = Setting::get('seeded_client_pages', []);

        if (isset($pages[$path])) {
            return;
        }

        $pages[$path] = [
            'hash' => md5_file($path),
            'route_check' => $routeCheck,
        ];
        Setting::set('seeded_client_pages', $pages);
    }

    /**
     * Insert a route line into routes/web.php if it is not already present.
     * Routes are inserted after the "new cached pages are inserted here" marker.
     */
    private function addRouteIfMissing(string $routeLine, string $routeCheck): void
    {
        $routesPath = base_path('routes/web.php');
        $contents = file_get_contents($routesPath);

        if (str_contains($contents, $routeCheck)) {
            return;
        }

        $contents = preg_replace(
            '/^(    \/\/ new cached pages are inserted here)$/m',
            "$1\n{$routeLine}",
            $contents,
            1
        );

        file_put_contents($routesPath, $contents);
    }

    /**
     * Build the opening PHP section for a Volt page component.
     */
    private function buildPhpSection(array $config): string
    {
        $layout = $config['layout'] ?? 'layouts.public';
        $params = $config['layout_params'] ?? [];
        $title = $config['title'] ?? null;
        $extraPhp = $config['extra_php'] ?? null;

        $paramsStr = implode(', ', array_map(
            fn ($k, $v) => "'{$k}' => '{$v}'",
            array_keys($params),
            array_values($params)
        ));

        $layoutAttr = empty($params)
            ? "#[Layout('{$layout}')]"
            : "#[Layout('{$layout}', [{$paramsStr}])]";

        $titleAttr = $title ? " #[Title('{$title}')]" : '';

        $body = $extraPhp ? "\n    {$extraPhp}\n" : '';

        return implode("\n", [
            '<?php',
            '',
            'use Livewire\Attributes\Layout;',
            'use Livewire\Attributes\Title;',
            'use Livewire\Component;',
            '',
            "new {$layoutAttr}{$titleAttr} class extends Component {{$body}}; ?>",
        ]);
    }
}
