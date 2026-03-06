<?php

use App\Support\ContentCache;
use App\Support\SchemaCache;
use App\Support\ShortcodeProcessor;
use Illuminate\Support\Facades\Storage;

if (! function_exists('content')) {
    /**
     * Retrieve a content override from the database, falling back to the given default.
     * On editor preview requests, unsaved draft values stored in the session take precedence.
     * For 'image' type, the stored path is converted to a public URL.
     * For 'text' and 'richtext' types, [[shortcode]] tags are expanded.
     *
     * For new-format slugs (templateName:randomId), type and default are resolved from the
     *
     * @schema block via SchemaCache when not explicitly provided.
     */
    function content(string $slug, string $key, ?string $default = null, ?string $type = null, string $group = ''): string
    {
        $resolvedType = $type ?? 'text';
        $resolvedDefault = $default ?? '';

        if (str_contains($slug, ':') && ($type === null || $default === null)) {
            $templateName = explode(':', $slug, 2)[0];
            $schemaField = app(SchemaCache::class)->getField($templateName, $key);

            if ($schemaField !== null) {
                $resolvedType = $type ?? $schemaField['type'];
                $resolvedDefault = $default ?? $schemaField['default'];
            }
        }

        if (request()->routeIs('design-library.preview')) {
            $drafts = session('editor_draft_overrides', []);
            $draftKey = $slug.':'.$key;

            if (array_key_exists($draftKey, $drafts)) {
                $draft = (string) ($drafts[$draftKey]['value'] ?? '');

                if ($draft === '') {
                    return $resolvedDefault;
                }

                return match ($resolvedType) {
                    'image' => Storage::url($draft),
                    'richtext' => ShortcodeProcessor::containsShortcodes($draft) ? ShortcodeProcessor::process($draft) : $draft,
                    'text' => ShortcodeProcessor::containsShortcodes($draft) ? ShortcodeProcessor::processRaw($draft) : $draft,
                    default => $draft,
                };
            }
        }

        $value = app(ContentCache::class)->get($slug, $key);

        if ($value === null) {
            return $resolvedDefault;
        }

        return match ($resolvedType) {
            'image' => Storage::url($value),
            'richtext' => ShortcodeProcessor::containsShortcodes($value) ? ShortcodeProcessor::process($value) : $value,
            'text' => ShortcodeProcessor::containsShortcodes($value) ? ShortcodeProcessor::processRaw($value) : $value,
            default => $value,
        };
    }
}
