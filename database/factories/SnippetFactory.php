<?php

namespace Database\Factories;

use App\Enums\SnippetType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Snippet>
 */
class SnippetFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(SnippetType::cases());

        return [
            'name' => fake()->words(3, true),
            'type' => $type,
            'placement' => $type->defaultPlacement(),
            'content' => '<!-- '.fake()->sentence().' -->',
            'page_path' => null,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
