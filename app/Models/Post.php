<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'cta_buttons',
        'status',
        'published_at',
        'featured_image',
        'layout',
    ];

    protected static function booted(): void
    {
        static::saving(function (Post $post): void {
            if (empty($post->slug)) {
                $post->slug = static::uniqueSlug($post->title, $post->id);
            }
        });

        static::deleted(function (Post $post): void {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
        });
    }

    private static function uniqueSlug(string $title, ?int $excludeId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($excludeId, fn (Builder $q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'cta_buttons' => 'array',
        ];
    }

    /** @param Builder<Post> $query */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published');
    }

    /** @param Builder<Post> $query */
    public function scopeAccessible(Builder $query): void
    {
        $query->whereIn('status', ['published', 'unlisted']);
    }

    public function featuredImageUrl(): ?string
    {
        return $this->featured_image ? Storage::disk('public')->url($this->featured_image) : null;
    }
}
