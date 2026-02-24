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
            'website_category' => $category,
            'description' => fake()->sentence(),
            'blade_code' => '<div class="min-h-screen"><section class="py-20"><h1 class="text-5xl font-bold">'.fake()->sentence(3).'</h1></section></div>',
            'php_code' => null,
            'source_file' => 'pages/'.$category->value.'/'.$slug.'.blade.php',
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
