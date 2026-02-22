<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6, false);
        $title = rtrim($title, '.');

        return [
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(20),
            'content' => implode("\n\n", fake()->paragraphs(5)),
            'status' => 'draft',
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function unlisted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'unlisted',
            'published_at' => now(),
        ]);
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'unpublished',
            'published_at' => null,
        ]);
    }

    public function withoutCategory(): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => null,
        ]);
    }
}
