<?php

namespace Database\Seeders;

use App\Jobs\IndexDesignLibraryJob;
use App\Models\DesignPage;
use App\Models\DesignRow;
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
            ],
            [
                'dl_source' => 'pages/custom/about.blade.php',
                'dest' => 'pages/⚡about.blade.php',
                'layout' => 'layouts.public',
                'title' => 'About',
            ],
            [
                'dl_source' => 'pages/custom/contact.blade.php',
                'dest' => 'pages/⚡contact.blade.php',
                'layout' => 'layouts.public',
                'title' => 'Contact',
            ],
            [
                'dl_source' => 'pages/custom/services.blade.php',
                'dest' => 'pages/⚡services.blade.php',
                'layout' => 'layouts.public',
                'title' => 'Services',
            ],
            [
                'dl_source' => 'pages/custom/locations.blade.php',
                'dest' => 'pages/⚡locations.blade.php',
                'layout' => 'layouts.public',
                'title' => 'Locations',
                'extra_php' => "public string \$pageName = 'Locations';",
            ],
            [
                'dl_source' => 'pages/custom/404.blade.php',
                'dest' => 'pages/⚡404.blade.php',
                'layout' => 'layouts.public',
                'layout_params' => ['status' => 'unlisted'],
                'title' => '404',
            ],
            [
                'dl_source' => 'pages/custom/blog-index.blade.php',
                'dest' => 'pages/blog/⚡index.blade.php',
                'layout' => 'layouts.public',
                'layout_params' => ['description' => 'Read our latest articles, insights, and updates.'],
                'title' => 'Blog',
                'extra_php' => "public string \$pageName = 'Blog';",
            ],
            [
                'dl_source' => 'pages/custom/blog-show.blade.php',
                'dest' => 'pages/blog/⚡show.blade.php',
                'layout' => 'layouts.public',
                // No title — the blog-post-article PHP block injects a dynamic title() method.
            ],
        ];

        $voltService = new VoltFileService;

        foreach ($pages as $config) {
            $destPath = resource_path("views/{$config['dest']}");

            if (file_exists($destPath)) {
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
        }
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
            : "#[Layout('{$layout}', [{$paramsStr}]]";

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
