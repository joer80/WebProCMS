<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\ResponseCache\Facades\ResponseCache;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'parent_event_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'cta_buttons',
        'gallery_images',
        'gallery_columns',
        'status',
        'published_at',
        'featured_image',
        'featured_image_alt',
        'layout',
        'meta_title',
        'meta_description',
        'is_noindex',
        'og_title',
        'og_description',
        'og_image',
        'is_seeded',
        'start_date',
        'end_date',
        'is_all_day',
        'timezone',
        'venue_name',
        'venue_address',
        'website_url',
        'cost',
        'is_repeating',
        'repeat_frequency',
        'repeat_interval',
        'repeat_ends_at',
        'repeat_days',
    ];

    protected static function booted(): void
    {
        static::saving(function (Event $event): void {
            if (empty($event->slug)) {
                $event->slug = static::uniqueSlug($event->title, $event->id);
            }
        });

        static::saved(function (Event $event): void {
            ResponseCache::clear();

            if ($event->is_repeating && $event->wasChanged(['is_repeating', 'repeat_frequency', 'repeat_interval', 'repeat_ends_at', 'repeat_days', 'start_date'])) {
                \App\Jobs\GenerateRepeatingEventChildrenJob::dispatch($event);
            }
        });

        static::deleted(function (Event $event): void {
            if ($event->featured_image) {
                Storage::disk('public')->delete($event->featured_image);
            }

            if ($event->gallery_images) {
                $paths = array_filter(array_map(
                    fn ($item) => is_string($item) ? $item : ($item['path'] ?? null),
                    $event->gallery_images,
                ));
                Storage::disk('public')->delete($paths);
            }

            ResponseCache::clear();
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

    /** @return HasMany<Event, $this> */
    public function childEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'parent_event_id');
    }

    /** @return BelongsTo<Event, $this> */
    public function parentEvent(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'parent_event_id');
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'repeat_ends_at' => 'date',
            'repeat_days' => 'array',
            'cta_buttons' => 'array',
            'gallery_images' => 'array',
            'gallery_columns' => 'integer',
            'is_noindex' => 'boolean',
            'is_seeded' => 'boolean',
            'is_all_day' => 'boolean',
            'is_repeating' => 'boolean',
        ];
    }

    /** @param Builder<Event> $query */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published');
    }

    /** @param Builder<Event> $query */
    public function scopeAccessible(Builder $query): void
    {
        $query->whereIn('status', ['published', 'unlisted']);
    }

    /** @param Builder<Event> $query */
    public function scopeUpcoming(Builder $query): void
    {
        $query->where('start_date', '>=', now());
    }

    /** @param Builder<Event> $query */
    public function scopePast(Builder $query): void
    {
        $query->where('start_date', '<', now());
    }

    /** @param Builder<Event> $query */
    public function scopeParent(Builder $query): void
    {
        $query->whereNull('parent_event_id');
    }

    public function featuredImageUrl(): ?string
    {
        return $this->featured_image ? Storage::disk('public')->url($this->featured_image) : null;
    }

    /** @return list<string> */
    public function galleryImageUrls(): array
    {
        return array_map(
            function ($item) {
                $path = is_string($item) ? $item : ($item['path'] ?? '');

                return Storage::disk('public')->url($path);
            },
            $this->gallery_images ?? []
        );
    }

    /** @return list<array{url: string, alt: string}> */
    public function galleryImagesData(): array
    {
        return array_values(array_map(
            function ($item) {
                $path = is_string($item) ? $item : ($item['path'] ?? '');
                $alt = is_string($item) ? '' : ($item['alt'] ?? '');

                return ['url' => Storage::disk('public')->url($path), 'alt' => $alt];
            },
            $this->gallery_images ?? []
        ));
    }
}
