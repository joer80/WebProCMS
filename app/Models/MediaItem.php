<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Spatie\ResponseCache\Facades\ResponseCache;

class MediaItem extends Model
{
    /** @use HasFactory<\Database\Factories\MediaItemFactory> */
    use HasFactory;

    protected $fillable = [
        'media_category_id',
        'path',
        'filename',
        'alt',
        'sort_order',
        'size',
        'mime_type',
    ];

    protected static function booted(): void
    {
        static::deleted(function (MediaItem $item): void {
            Storage::disk('public')->delete($item->path);
            ResponseCache::clear();
        });

        static::saved(function (): void {
            ResponseCache::clear();
        });
    }

    /** @return BelongsTo<MediaCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(MediaCategory::class);
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'size' => 'integer',
        ];
    }
}
