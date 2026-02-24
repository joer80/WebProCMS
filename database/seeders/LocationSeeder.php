<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('locations');

        Location::firstOrCreate(
            ['name' => 'GetRows Austin'],
            [
                'address' => '100 Congress Avenue',
                'city' => 'Austin',
                'state' => 'TX',
                'zip' => '78701',
                'phone' => '(512) 555-0101',
                'photo' => $this->downloadImage(10),
                'is_seeded' => true,
            ]
        );
    }

    private function downloadImage(int $id): ?string
    {
        $response = Http::get("https://picsum.photos/id/{$id}/600/400");

        if (! $response->successful()) {
            return null;
        }

        $path = 'locations/'.Str::random(40).'.jpg';
        Storage::disk('public')->put($path, $response->body());

        return $path;
    }
}
