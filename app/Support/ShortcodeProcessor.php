<?php

namespace App\Support;

use App\Models\Shortcode;
use Illuminate\Database\Eloquent\Collection;

class ShortcodeProcessor
{
    /** @var array<string, mixed>|null Current content item field data for [[field:key]] resolution */
    private static ?array $itemContext = null;

    /**
     * Set the current content item's field data so [[field:key]] shortcodes resolve correctly.
     * Call this in the Volt component's mount() before the page renders.
     *
     * @param  array<string, mixed>  $data
     */
    public static function setItemContext(array $data): void
    {
        self::$itemContext = $data;
    }

    public static function clearItemContext(): void
    {
        self::$itemContext = null;
    }

    /**
     * Load active shortcodes, cached for the duration of the request (once() resets between tests).
     *
     * @return Collection<string, Shortcode>
     */
    private static function getShortcodes(): Collection
    {
        return once(fn (): Collection => Shortcode::query()
            ->where('is_active', true)
            ->get()
            ->keyBy('tag'));
    }

    /**
     * Process content by replacing [[tag]] shortcodes with their values.
     *
     * Plain text portions are HTML-escaped. Rich text and PHP code shortcodes
     * are inserted raw, so the returned string should be rendered unescaped.
     * DB shortcodes take priority; unknown tags fall back to system shortcodes.
     */
    public static function process(string $content): string
    {
        /** @var Collection<string, Shortcode> $shortcodes */
        $shortcodes = self::getShortcodes();

        $parts = preg_split('/(\[\[[\w-]+(?::[\w_-]+)?\]\])/', $content, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];

        $result = '';

        foreach ($parts as $part) {
            if (preg_match('/^\[\[([\w-]+(?::[\w_-]+)?)\]\]$/', $part, $matches)) {
                $tag = $matches[1];

                if (str_starts_with($tag, 'field:') && self::$itemContext !== null) {
                    $fieldKey = substr($tag, 6);
                    $result .= e((string) (self::$itemContext[$fieldKey] ?? ''));

                    continue;
                }

                $shortcode = $shortcodes->get($tag);

                if ($shortcode) {
                    $result .= match ($shortcode->type) {
                        'single_text' => e($shortcode->content ?? ''),
                        'rich_text' => $shortcode->content ?? '',
                        'php_code' => self::evaluatePhpCode($shortcode->content ?? ''),
                        default => e($part),
                    };
                } else {
                    $systemValue = SystemShortcodes::resolve($tag);
                    $result .= $systemValue !== null ? e($systemValue) : e($part);
                }
            } else {
                $result .= e($part);
            }
        }

        return $result;
    }

    /**
     * Replace [[tag]] shortcodes with their raw content — no HTML escaping applied.
     * Use this when the caller (e.g. a Blade component) will handle escaping itself.
     * DB shortcodes take priority; unknown tags fall back to system shortcodes.
     */
    public static function processRaw(string $content): string
    {
        /** @var Collection<string, Shortcode> $shortcodes */
        $shortcodes = self::getShortcodes();

        $parts = preg_split('/(\[\[[\w-]+(?::[\w_-]+)?\]\])/', $content, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];

        $result = '';

        foreach ($parts as $part) {
            if (preg_match('/^\[\[([\w-]+(?::[\w_-]+)?)\]\]$/', $part, $matches)) {
                $tag = $matches[1];

                if (str_starts_with($tag, 'field:') && self::$itemContext !== null) {
                    $fieldKey = substr($tag, 6);
                    $result .= (string) (self::$itemContext[$fieldKey] ?? '');

                    continue;
                }

                $shortcode = $shortcodes->get($tag);

                if ($shortcode) {
                    $result .= match ($shortcode->type) {
                        'php_code' => self::evaluatePhpCode($shortcode->content ?? ''),
                        default => $shortcode->content ?? '',
                    };
                } else {
                    $result .= SystemShortcodes::resolve($tag) ?? $part;
                }
            } else {
                $result .= $part;
            }
        }

        return $result;
    }

    /**
     * Check whether content contains any shortcode tags.
     */
    public static function containsShortcodes(string $content): bool
    {
        return (bool) preg_match('/\[\[[\w-]+(?::[\w_-]+)?\]\]/', $content);
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
