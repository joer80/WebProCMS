<?php

namespace App\Support;

use App\Models\DesignRow;

class SchemaCache
{
    /** @var array<string, array<string, array{key: string, type: string, group: string, default: string, label: string}>> */
    private array $cache = [];

    /**
     * Get a single field definition for the given template name and field key.
     *
     * @return array{key: string, type: string, group: string, default: string, label: string}|null
     */
    public function getField(string $templateName, string $key): ?array
    {
        return $this->getFieldsKeyed($templateName)[$key] ?? null;
    }

    /**
     * Get all schema fields for a template as an ordered array (for editor sidebar).
     *
     * @return list<array{key: string, type: string, group: string, default: string, label: string, slug: string}>
     */
    public function getFieldsForRow(string $templateName): array
    {
        return array_values($this->getFieldsKeyed($templateName));
    }

    /**
     * Get schema fields keyed by field key, loading from DB on first access.
     *
     * @return array<string, array{key: string, type: string, group: string, default: string, label: string}>
     */
    private function getFieldsKeyed(string $templateName): array
    {
        if (! array_key_exists($templateName, $this->cache)) {
            $designRow = DesignRow::query()
                ->whereRaw('source_file LIKE ?', ["%/{$templateName}.blade.php"])
                ->first();

            $fields = $designRow?->schema_fields ?? [];

            $this->cache[$templateName] = collect($fields)->keyBy('key')->all();
        }

        return $this->cache[$templateName];
    }
}
