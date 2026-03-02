<?php

namespace App\Support;

use App\Models\ContentOverride;

class ContentCache
{
    /** @var array<string, array<string, string>> */
    private array $slugCache = [];

    /**
     * Retrieve a single field value for a slug, loading all fields for that slug
     * in one query on first access.
     */
    public function get(string $slug, string $key): ?string
    {
        if (! array_key_exists($slug, $this->slugCache)) {
            $this->slugCache[$slug] = ContentOverride::where('row_slug', $slug)
                ->pluck('value', 'key')
                ->all();
        }

        return $this->slugCache[$slug][$key] ?? null;
    }
}
