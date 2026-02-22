<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $categories = Category::factory(5)->create();

        Post::factory(85)
            ->published()
            ->recycle($categories)
            ->create();

        Post::factory(15)
            ->draft()
            ->recycle($categories)
            ->create();
    }
}
