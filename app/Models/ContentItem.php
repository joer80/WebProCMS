<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContentItem extends Model
{
    protected $fillable = [
        'type_slug',
        'title',
        'data',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (ContentItem $item): void {
            $item->title = static::deriveTitle($item);
        });
    }

    private static function deriveTitle(ContentItem $item): ?string
    {
        $data = $item->data ?? [];

        if (empty($data)) {
            return null;
        }

        $typeDef = ContentTypeDefinition::where('slug', $item->type_slug)->first();

        if (! $typeDef) {
            return null;
        }

        foreach ($typeDef->fields as $field) {
            $type = $field['type'] ?? 'text';
            $name = $field['name'] ?? '';

            if (in_array($type, ['text', 'richtext', 'richtext_tiptap']) && isset($data[$name]) && $data[$name] !== '') {
                $value = strip_tags((string) $data[$name]);

                return mb_substr(trim($value), 0, 200) ?: null;
            }
        }

        return null;
    }

    /** @param Builder<ContentItem> $query */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published');
    }

    public function displayTitle(): string
    {
        return $this->title ?: 'Untitled';
    }
}
