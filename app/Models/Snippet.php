<?php

namespace App\Models;

use App\Enums\SnippetPlacement;
use App\Enums\SnippetType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\ResponseCache\Facades\ResponseCache;

class Snippet extends Model
{
    /** @use HasFactory<\Database\Factories\SnippetFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'placement',
        'content',
        'page_path',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => SnippetType::class,
            'placement' => SnippetPlacement::class,
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => ResponseCache::clear());
        static::deleted(fn () => ResponseCache::clear());
    }

    public static function forPage(string $path): Builder
    {
        $normalizedPath = trim($path, '/');

        return static::query()
            ->where('is_active', true)
            ->where(function (Builder $q) use ($normalizedPath): void {
                $q->whereNull('page_path')
                    ->orWhere('page_path', $normalizedPath)
                    ->orWhere('page_path', '/'.$normalizedPath);
            })
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
