<?php

namespace App\Support;

class DesignLibraryService
{
    /**
     * Parse a design library .blade.php template file into structured data.
     *
     * @return array{name: string, description: string, sort_order: int, blade_code: string, php_code: string, source_file: string}
     */
    public function parseTemplateFile(string $fullPath): array
    {
        $contents = file_get_contents($fullPath);

        $name = '';
        $description = '';
        $sortOrder = 0;
        $phpCode = '';

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

        return [
            'name' => $name ?: basename($fullPath, '.blade.php'),
            'description' => $description,
            'sort_order' => $sortOrder,
            'blade_code' => $bladeCode,
            'php_code' => $phpCode,
            'source_file' => $this->relativeSourcePath($fullPath),
        ];
    }

    /**
     * Build a .blade.php template file string from structured data.
     *
     * @param  array{name: string, description: string, sort_order: int, blade_code: string, php_code: string}  $data
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
