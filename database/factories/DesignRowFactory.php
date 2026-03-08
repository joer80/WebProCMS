<?php

namespace Database\Factories;

use App\Enums\RowCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DesignRow>
 */
class DesignRowFactory extends Factory
{
    public function definition(): array
    {
        $category = fake()->randomElement(RowCategory::cases());
        $slug = $category->value.'-'.fake()->lexify('??????');

        return [
            'name' => fake()->words(3, true),
            'category' => $category,
            'description' => fake()->sentence(),
            'source_file' => 'rows/'.$category->value.'/'.$slug.'.blade.php',
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
