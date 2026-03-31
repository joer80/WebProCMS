<?php

namespace Database\Factories;

use App\Enums\PageCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DesignPage>
 */
class DesignPageFactory extends Factory
{
    public function definition(): array
    {
        $category = fake()->randomElement(PageCategory::cases());
        $slug = $category->value.'-'.fake()->lexify('??????');

        return [
            'name' => fake()->words(3, true),
            'categories' => [$category->value],
            'description' => fake()->sentence(),
            'blade_code' => null,
            'php_code' => null,
            'row_names' => [],
            'source_file' => 'pages/'.$category->value.'/'.$slug.'.blade.php',
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
