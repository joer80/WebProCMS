<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
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
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(20),
            'content' => implode("\n\n", fake()->paragraphs(5)),
            'status' => 'draft',
            'published_at' => null,
            'layout' => 'image-top',
            'start_date' => now()->addDays(fake()->numberBetween(1, 30)),
            'end_date' => null,
            'is_all_day' => false,
            'is_repeating' => false,
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

    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->addDays(fake()->numberBetween(1, 30)),
        ]);
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->subDays(fake()->numberBetween(1, 30)),
            'status' => 'published',
            'published_at' => now()->subDays(fake()->numberBetween(30, 60)),
        ]);
    }

    public function allDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_all_day' => true,
        ]);
    }

    public function withVenue(): static
    {
        return $this->state(fn (array $attributes) => [
            'venue_name' => fake()->company(),
            'venue_address' => fake()->address(),
        ]);
    }
}
