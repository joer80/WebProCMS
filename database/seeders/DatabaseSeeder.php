<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')],
        );

        $categories = collect(['General', 'News', 'Events'])->map(
            fn (string $name) => Category::firstOrCreate(['name' => $name])
        );

        $imagePaths = $this->downloadImages(20);

        Post::factory(85)
            ->published()
            ->recycle($categories)
            ->sequence(fn (Sequence $sequence) => ['featured_image' => $imagePaths[$sequence->index % count($imagePaths)]])
            ->create();

        Post::factory(15)
            ->draft()
            ->recycle($categories)
            ->sequence(fn (Sequence $sequence) => ['featured_image' => $imagePaths[$sequence->index % count($imagePaths)]])
            ->create();
    }

    /** @return array<int, string> */
    private function downloadImages(int $count): array
    {
        Storage::disk('public')->makeDirectory('posts');

        $paths = [];

        for ($i = 1; $i <= $count; $i++) {
            $response = Http::get("https://picsum.photos/id/{$i}/1200/630");

            if ($response->successful()) {
                $path = 'posts/'.Str::random(40).'.jpg';
                Storage::disk('public')->put($path, $response->body());
                $paths[] = $path;
            }
        }

        return $paths;
    }
}
