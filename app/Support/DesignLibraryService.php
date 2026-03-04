<?php

namespace App\Support;

use Illuminate\Support\Str;

class DesignLibraryService
{
    /**
     * Parse a design library .blade.php template file into structured data.
     *
     * @return array{name: string, description: string, sort_order: int, blade_code: string, php_code: string, source_file: string, schema_fields: list<array{key: string, type: string, group: string, default: string, label: string}>}
     */
    public function parseTemplateFile(string $fullPath): array
    {
        $contents = file_get_contents($fullPath);

        $name = '';
        $description = '';
        $sortOrder = 0;
        $phpCode = '';
        $schemaFields = [];

        // Extract frontmatter from the first {{-- ... --}} block
        if (preg_match('/^\{\{--\s*(.*?)\s*--\}\}/s', $contents, $frontmatterMatch)) {
            $frontmatter = $frontmatterMatch[1];

            if (preg_match('/@name\s+(.+)/i', $frontmatter, $m)) {
                $name = trim($m[1]);
            }

            if (preg_match('/@description\s+(.+)/i', $frontmatter, $m)) {
                $description = trim($m[1]);
            }

            if (preg_match('/@sort\s+(\d+)/i', $frontmatter, $m)) {
                $sortOrder = (int) $m[1];
            }

            // Remove the frontmatter block from content
            $contents = preg_replace('/^\{\{--\s*.*?\s*--\}\}\s*/s', '', $contents, 1);
        }

        // Extract optional trailing {{-- @php ... --}} block
        if (preg_match('/\{\{--\s*@php\s*(.*?)\s*--\}\}\s*$/s', $contents, $phpMatch)) {
            $phpCode = trim($phpMatch[1]);
            $contents = preg_replace('/\s*\{\{--\s*@php\s*.*?\s*--\}\}\s*$/s', '', $contents);
        }

        $bladeCode = trim($contents);
        $schemaFields = $this->parseSchemaFields($bladeCode);

        return [
            'name' => $name ?: basename($fullPath, '.blade.php'),
            'description' => $description,
            'sort_order' => $sortOrder,
            'blade_code' => $bladeCode,
            'php_code' => $phpCode,
            'source_file' => $this->relativeSourcePath($fullPath),
            'schema_fields' => $schemaFields,
        ];
    }

    /**
     * Parse editable field definitions from <x-dl-*> component tags and @dlItems directives in blade code.
     * Infers type and group from key naming conventions:
     *  - toggle_*  → toggle
     *  - grid_*    → grid
     *  - *_new_tab → toggle
     *  - *_classes → classes
     *  - *_image or image → image
     *  - *_url, *_alt → text (group strips suffix)
     *  - anything else → text
     *
     * Fields are merged in document order (by byte offset).
     *
     * @return list<array{key: string, type: string, group: string, default: string, label: string}>
     */
    public function parseSchemaFields(string $bladeCode): array
    {
        $seen = [];
        $items = []; // [['offset' => int, 'fields' => [...]]]

        // 1. Collect standalone @dlItems directive matches (used without x-dl-grid, e.g. Alpine sliders).
        preg_match_all(
            "/@dlItems\('__SLUG__',\s*'([^']+)',\s*\\\$[\w]+(?:,\s*'((?:[^'\\\\]|\\\\.)*)')?\)/",
            $bladeCode,
            $dlItemsMatches,
            PREG_SET_ORDER | PREG_OFFSET_CAPTURE
        );

        foreach ($dlItemsMatches as $match) {
            $prefix = $match[1][0];
            $key = "grid_{$prefix}";
            $default = isset($match[2]) ? stripslashes($match[2][0]) : '';
            $offset = $match[0][1];

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            [$type, $group] = $this->inferTypeAndGroup($key);

            $items[] = [
                'offset' => $offset,
                'fields' => [[
                    'key' => $key,
                    'type' => $type,
                    'group' => $group,
                    'default' => $default,
                    'label' => ucwords(str_replace('_', ' ', $key)),
                ]],
            ];
        }

        // 2. Collect <x-dl.*> component tag matches with their document offsets.
        preg_match_all(
            '/<x-dl\.([\w-]+)(.*?)\s*\/?>/s',
            $bladeCode,
            $tagMatches,
            PREG_SET_ORDER | PREG_OFFSET_CAPTURE
        );

        foreach ($tagMatches as $tagMatch) {
            $componentSlug = $tagMatch[1][0];
            $attrsString = $tagMatch[2][0];
            $offset = $tagMatch[0][1];

            $className = 'App\\View\\Components\\Dl\\'.Str::studly($componentSlug);

            if (! class_exists($className) || ! method_exists($className, 'schemaFields')) {
                continue;
            }

            $attrs = [];
            preg_match_all('/(\w[\w-]*)=(["\'])(.*?)\2/s', $attrsString, $attrMatches, PREG_SET_ORDER);

            foreach ($attrMatches as $attrMatch) {
                $attrs[$attrMatch[1]] = $attrMatch[3];
            }

            $newFields = [];

            foreach ($className::schemaFields($attrs) as $field) {
                if (isset($seen[$field['key']])) {
                    continue;
                }

                $seen[$field['key']] = true;
                [$type, $group] = $this->inferTypeAndGroup($field['key']);

                $newFields[] = [
                    'key' => $field['key'],
                    'type' => $type,
                    'group' => $field['group'] ?? $group,
                    'default' => $field['default'],
                    'label' => $field['label'] ?? ucwords(str_replace('_', ' ', $field['key'])),
                ];
            }

            if ($newFields) {
                $items[] = [
                    'offset' => $offset,
                    'fields' => $newFields,
                ];
            }
        }

        // Sort by document offset to preserve natural reading order.
        usort($items, fn ($a, $b) => $a['offset'] <=> $b['offset']);

        $fields = [];

        foreach ($items as $item) {
            foreach ($item['fields'] as $field) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    /**
     * Infer the field type and group from the key name.
     *
     * @return array{string, string} [type, group]
     */
    private function inferTypeAndGroup(string $key): array
    {
        if (str_starts_with($key, 'toggle_')) {
            return ['toggle', substr($key, 7)];
        }

        if (str_starts_with($key, 'grid_')) {
            return ['grid', substr($key, 5)];
        }

        if (str_ends_with($key, '_new_tab')) {
            return ['toggle', substr($key, 0, -8)];
        }

        if (str_ends_with($key, '_classes')) {
            return ['classes', substr($key, 0, -8)];
        }

        if (str_ends_with($key, '_image') || $key === 'image') {
            return ['image', $key === 'image' ? 'media' : substr($key, 0, -6)];
        }

        if (str_ends_with($key, '_url')) {
            return ['text', substr($key, 0, -4)];
        }

        if ($key === 'image_alt') {
            return ['text', 'media'];
        }

        if (str_ends_with($key, '_image_alt')) {
            return ['text', substr($key, 0, -10)];
        }

        if (str_ends_with($key, '_alt')) {
            return ['text', substr($key, 0, -4)];
        }

        if (str_ends_with($key, '_htag')) {
            return ['text', substr($key, 0, -5)];
        }

        if (str_ends_with($key, '_id')) {
            return ['id', substr($key, 0, -3)];
        }

        if (str_ends_with($key, '_attrs')) {
            return ['attrs', substr($key, 0, -6)];
        }

        return ['text', $key];
    }

    /**
     * Build a .blade.php template file string from structured data.
     *
     * @param  array{name: string, description: string, sort_order: int, blade_code: string, php_code: string, schema_fields?: list<array{key: string, type: string, group: string, default: string}>}  $data
     */
    public function buildTemplateFile(array $data): string
    {
        $lines = [];
        $lines[] = '{{--';
        $lines[] = '@name '.$data['name'];

        if (! empty($data['description'])) {
            $lines[] = '@description '.$data['description'];
        }

        if (! empty($data['sort_order'])) {
            $lines[] = '@sort '.$data['sort_order'];
        }

        $lines[] = '--}}';
        $lines[] = $data['blade_code'];

        if (! empty($data['php_code'])) {
            $lines[] = '{{--';
            $lines[] = '@php';
            $lines[] = $data['php_code'];
            $lines[] = '--}}';
        }

        return implode("\n", $lines)."\n";
    }

    /**
     * Scan a directory for .blade.php template files, grouped by subfolder name.
     *
     * @return array<string, array<int, string>> folder => [fullPaths]
     */
    public function scanDirectory(string $baseDir): array
    {
        $results = [];

        if (! is_dir($baseDir)) {
            return $results;
        }

        foreach (glob($baseDir.'/*/') as $categoryDir) {
            $category = basename($categoryDir);
            $files = glob($categoryDir.'*.blade.php') ?: [];

            foreach ($files as $file) {
                $results[$category][] = $file;
            }
        }

        return $results;
    }

    /**
     * Write a template back to its source file on disk.
     *
     * @param  array{name: string, description: string, sort_order: int, blade_code: string, php_code: string}  $data
     */
    public function writeTemplateFile(string $fullPath, array $data): void
    {
        $directory = dirname($fullPath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($fullPath, $this->buildTemplateFile($data));
    }

    /**
     * Get the relative source path from resources/design-library/.
     */
    public function relativeSourcePath(string $fullPath): string
    {
        $base = resource_path('design-library').'/';

        return str_starts_with($fullPath, $base)
            ? substr($fullPath, strlen($base))
            : $fullPath;
    }

    /**
     * Get the full path for a given source_file relative path.
     */
    public function fullPath(string $sourceFile): string
    {
        return resource_path('design-library/'.$sourceFile);
    }
}
