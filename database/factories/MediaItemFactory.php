<?php

namespace Database\Factories;

use App\Models\MediaCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MediaItem>
 */
class MediaItemFactory extends Factory
{
    public function definition(): array
    {
        $filename = Str::random(20).'.jpg';

        return [
            'media_category_id' => MediaCategory::factory(),
            'path' => 'uncategorized/'.$filename,
            'filename' => $filename,
            'alt' => fake()->sentence(4),
            'sort_order' => fake()->numberBetween(0, 100),
            'size' => fake()->numberBetween(10000, 2000000),
            'mime_type' => 'image/jpeg',
        ];
    }
}
