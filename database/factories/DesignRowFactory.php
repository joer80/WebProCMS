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
            'blade_code' => '<section class="py-12"><div class="container mx-auto"><h2 class="text-3xl font-bold">'.fake()->sentence(4).'</h2></div></section>',
            'php_code' => null,
            'source_file' => 'rows/'.$category->value.'/'.$slug.'.blade.php',
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
