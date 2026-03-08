<?php

namespace App\Support;

use App\Enums\RowCategory;
use App\Models\DesignRow;
use Illuminate\Support\Collection;

class LayoutService
{
    private const CONFIG_FILE = 'layout.php';

    /**
     * Read the current layout config, merging with defaults.
     *
     * @return array{active_header: string|null, active_footer: string|null, body_classes: string, php_top: string}
     */
    public function getConfig(): array
    {
        $defaults = [
            'active_header' => null,
            'active_footer' => null,
            'body_classes' => '',
            'php_top' => '',
        ];

        $path = config_path(self::CONFIG_FILE);

        if (! file_exists($path)) {
            return $defaults;
        }

        $loaded = include $path;

        return is_array($loaded) ? array_merge($defaults, $loaded) : $defaults;
    }

    /**
     * Merge updates into the current layout config and write it to disk.
     *
     * @param  array<string, mixed>  $updates
     */
    public function writeConfig(array $updates): void
    {
        $current = $this->getConfig();
        $merged = array_merge($current, $updates);

        $exports = var_export($merged, true);
        $content = <<<PHP
<?php

/*
|--------------------------------------------------------------------------
| Layout
|--------------------------------------------------------------------------
|
| Controls the public site layout: active header/footer templates,
| body tag classes, and global PHP code that runs on every public page.
| Edit these settings via the dashboard at /dashboard/templates.
|
| Keys:
|   active_header  - template name e.g. 'header-simple', or null for built-in
|   active_footer  - template name e.g. 'footer-simple', or null for built-in
|   body_classes   - additional classes appended to the <body> tag
|   php_top        - PHP code eval()'d before the DOCTYPE on every public page
|
*/

return $exports;
PHP;

        file_put_contents(config_path(self::CONFIG_FILE), $content);

        // Keep the in-memory config cache in sync so wire:navigate SPA
        // navigation picks up the new values without a full PHP restart.
        app('config')->set('layout', $merged);
    }

    /**
     * Return all available header templates from the design library.
     *
     * @return Collection<int, DesignRow>
     */
    public function getAvailableHeaders(): Collection
    {
        return DesignRow::query()
            ->where('category', RowCategory::Header->value)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Return all available footer templates from the design library.
     *
     * @return Collection<int, DesignRow>
     */
    public function getAvailableFooters(): Collection
    {
        return DesignRow::query()
            ->where('category', RowCategory::Footer->value)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Activate a header template: copy it to layouts/partials/header.blade.php
     * with a fixed slug and update the layout config.
     */
    public function activateHeader(DesignRow $row): void
    {
        $this->activateTemplate($row, 'header');
    }

    /**
     * Activate a footer template: copy it to layouts/partials/footer.blade.php
     * with a fixed slug and update the layout config.
     */
    public function activateFooter(DesignRow $row): void
    {
        $this->activateTemplate($row, 'footer');
    }

    /**
     * Deactivate the custom header, reverting to the built-in layout header.
     */
    public function deactivateHeader(): void
    {
        $this->writeConfig(['active_header' => null]);
    }

    /**
     * Deactivate the custom footer, reverting to the built-in layout footer.
     */
    public function deactivateFooter(): void
    {
        $this->writeConfig(['active_footer' => null]);
    }

    /**
     * Derive the template name (slug prefix) from a DesignRow source_file.
     * e.g. "rows/header/header-simple.blade.php" -> "header-simple"
     */
    public function templateName(DesignRow $row): string
    {
        return basename($row->source_file, '.blade.php');
    }

    /**
     * Copy the design library template to a local layout partial file with a fixed slug.
     */
    private function activateTemplate(DesignRow $row, string $type): void
    {
        $name = $this->templateName($row);
        $slug = "{$name}:{$type}";

        $sourceFile = resource_path('design-library/'.$row->source_file);
        $bladeCode = $this->stripFrontmatter(file_get_contents($sourceFile));
        $bladeCode = str_replace('__SLUG__', $slug, $bladeCode);

        $fileContent = "<?php /** @layout-partial {$type} */ ?>\n"
            ."<div class=\"contents\">\n"
            ."{{-- ROW:start:{$slug} --}}\n"
            .trim($bladeCode)."\n"
            ."{{-- ROW:end:{$slug} --}}\n"
            ."</div>\n";

        $dir = resource_path('views/layouts/partials');

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents("{$dir}/{$type}.blade.php", $fileContent);

        $this->writeConfig(["active_{$type}" => $name]);
    }

    /**
     * Strip the leading frontmatter comment block from blade code.
     */
    private function stripFrontmatter(string $bladeCode): string
    {
        return preg_replace('/^\{\{--.*?--\}\}\s*/s', '', $bladeCode) ?? $bladeCode;
    }
}
