<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentTypeDefinition extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'singular',
        'icon',
        'fields',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'fields' => 'array',
            'sort_order' => 'integer',
        ];
    }

    /** @return Collection<int, static> */
    public static function allOrdered(): Collection
    {
        return static::orderBy('sort_order')->orderBy('name')->get();
    }

    /** @return HasMany<ContentItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(ContentItem::class, 'type_slug', 'slug');
    }
}
