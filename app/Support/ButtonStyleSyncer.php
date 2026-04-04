<?php

namespace App\Support;

/**
 * Writes saved button-variant class strings back into buttons.css so the
 *
 * @apply values are up-to-date for the next Tailwind build.
 */
class ButtonStyleSyncer
{
    private string $path;

    public function __construct()
    {
        $this->path = resource_path('css/buttons.css');
    }

    /**
     * Default class strings for each button variant, used when no saved value exists.
     *
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            'primary' => 'inline-flex items-center justify-center px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors',
            'secondary' => 'inline-flex items-center justify-center px-6 py-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 font-semibold rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors',
            'ghost' => 'inline-flex items-center justify-center px-6 py-3 text-zinc-600 dark:text-zinc-300 font-semibold hover:text-zinc-900 dark:hover:text-white transition-colors',
            'inverted' => 'inline-flex items-center justify-center px-8 py-3 bg-white text-primary font-semibold rounded-lg hover:bg-zinc-100 transition-colors',
            'outline_white' => 'inline-flex items-center justify-center px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors',
            'outline_dark' => 'inline-flex items-center justify-center px-6 py-3 border border-zinc-700 text-zinc-300 font-semibold rounded-lg hover:bg-zinc-800 transition-colors',
            'danger' => 'inline-flex items-center justify-center px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors',
        ];
    }

    /**
     * Sync all variants at once and write the file once.
     *
     * @param  array<string, string>  $variants  e.g. ['primary' => 'px-6 py-3 ...']
     */
    public function syncAll(array $variants): void
    {
        if (! file_exists($this->path)) {
            return;
        }

        $content = file_get_contents($this->path);

        foreach ($variants as $variant => $classes) {
            $content = $this->replace($content, $variant, $classes);
        }

        file_put_contents($this->path, $content);
    }

    /**
     * Sync a single variant.
     */
    public function sync(string $variant, string $classes): void
    {
        if (! file_exists($this->path)) {
            return;
        }

        $content = file_get_contents($this->path);
        $updated = $this->replace($content, $variant, $classes);

        if ($updated !== $content) {
            file_put_contents($this->path, $updated);
        }
    }

    private function replace(string $content, string $variant, string $classes): string
    {
        $variantName = str_replace('_', '-', $variant);
        $start = "/* @@btn-{$variantName}-start */";
        $end = "/* @@btn-{$variantName}-end */";

        $startPos = strpos($content, $start);
        $endPos = strpos($content, $end);

        if ($startPos === false || $endPos === false) {
            return $content;
        }

        $afterStart = $startPos + strlen($start);
        $endTagEnd = $endPos + strlen($end);

        $block = "\n@utility btn-{$variantName} {\n    @apply {$classes};\n}\n";

        return substr($content, 0, $afterStart)
            .$block
            .substr($content, $endTagEnd);
    }
}
