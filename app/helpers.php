<?php

use App\Models\ContentOverride;
use Illuminate\Support\Facades\Storage;

if (! function_exists('content')) {
    /**
     * Retrieve a content override from the database, falling back to the given default.
     * On editor preview requests, unsaved draft values stored in the session take precedence.
     * For 'image' type, the stored path is converted to a public URL.
     */
    function content(string $slug, string $key, string $default = '', string $type = 'text', string $group = ''): string
    {
        if (request()->routeIs('design-library.preview')) {
            $drafts = session('editor_draft_overrides', []);
            $draftKey = $slug.':'.$key;

            if (array_key_exists($draftKey, $drafts)) {
                $draft = (string) ($drafts[$draftKey]['value'] ?? '');

                if ($draft === '') {
                    return $default;
                }

                return match ($type) {
                    'image' => Storage::url($draft),
                    default => $draft,
                };
            }
        }

        $value = ContentOverride::where('row_slug', $slug)
            ->where('key', $key)
            ->value('value');

        if ($value === null) {
            return $default;
        }

        return match ($type) {
            'image' => Storage::url($value),
            default => $value,
        };
    }
}
