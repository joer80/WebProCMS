<?php

namespace App\Support;

use App\Models\Shortcode;
use Illuminate\Database\Eloquent\Collection;

class ShortcodeProcessor
{
    /**
     * Process content by replacing [[tag]] shortcodes with their values.
     *
     * Plain text portions are HTML-escaped. Rich text and PHP code shortcodes
     * are inserted raw, so the returned string should be rendered unescaped.
     */
    public static function process(string $content): string
    {
        /** @var Collection<int, Shortcode> $shortcodes */
        $shortcodes = Shortcode::query()
            ->where('is_active', true)
            ->get()
            ->keyBy('tag');

        $parts = preg_split('/(\[\[[\w-]+\]\])/', $content, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];

        $result = '';

        foreach ($parts as $part) {
            if (preg_match('/^\[\[([\w-]+)\]\]$/', $part, $matches)) {
                $tag = $matches[1];
                $shortcode = $shortcodes->get($tag);

                if ($shortcode) {
                    $result .= match ($shortcode->type) {
                        'single_text' => e($shortcode->content ?? ''),
                        'rich_text' => $shortcode->content ?? '',
                        'php_code' => self::evaluatePhpCode($shortcode->content ?? ''),
                        default => e($part),
                    };
                } else {
                    $result .= e($part);
                }
            } else {
                $result .= e($part);
            }
        }

        return $result;
    }

    /**
     * Check whether content contains any shortcode tags.
     */
    public static function containsShortcodes(string $content): bool
    {
        return (bool) preg_match('/\[\[[\w-]+\]\]/', $content);
    }

    private static function evaluatePhpCode(string $code): string
    {
        ob_start();

        try {
            eval($code);
        } catch (\Throwable) {
            ob_end_clean();

            return '';
        }

        return ob_get_clean() ?: '';
    }
}
