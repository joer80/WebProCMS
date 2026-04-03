<?php

namespace App\Support;

use App\Enums\RowCategory;
use App\Models\DesignRow;
use App\Models\Setting;
use Illuminate\Support\Collection;

class LayoutService
{
    /**
     * Read the current layout config, merging with defaults.
     *
     * @return array{active_header: string|null, active_footer: string|null, body_classes: string, php_top: string}
     */
    public function getConfig(): array
    {
        return [
            'active_header' => Setting::get('layout.active_header', '') ?: null,
            'active_footer' => Setting::get('layout.active_footer', '') ?: null,
            'body_classes' => Setting::get('layout.body_classes', ''),
            'php_top' => Setting::get('layout.php_top', ''),
        ];
    }

    /**
     * Merge updates into the current layout config and persist to the database.
     *
     * @param  array<string, mixed>  $updates
     */
    public function writeConfig(array $updates): void
    {
        $current = $this->getConfig();
        $merged = array_merge($current, $updates);

        Setting::set('layout.active_header', $merged['active_header'] ?? '');
        Setting::set('layout.active_footer', $merged['active_footer'] ?? '');
        Setting::set('layout.body_classes', $merged['body_classes'] ?? '');
        Setting::set('layout.php_top', $merged['php_top'] ?? '');

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
            ."{{-- ROW:start:{$slug} --}}\n"
            ."<div class=\"contents\">\n"
            .trim($bladeCode)."\n"
            ."</div>\n"
            ."{{-- ROW:end:{$slug} --}}\n";

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
