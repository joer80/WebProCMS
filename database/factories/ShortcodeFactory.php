<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shortcode>
 */
class ShortcodeFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        return [
            'name' => ucwords($name),
            'tag' => Str::slug($name, '_'),
            'type' => $this->faker->randomElement(['single_text', 'rich_text', 'php_code']),
            'content' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function singleText(): static
    {
        return $this->state(['type' => 'single_text']);
    }

    public function richText(): static
    {
        return $this->state(['type' => 'rich_text']);
    }

    public function phpCode(): static
    {
        return $this->state(['type' => 'php_code']);
    }
}
