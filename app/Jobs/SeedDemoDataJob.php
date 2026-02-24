<?php

namespace App\Jobs;

use App\Models\Location;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;

class SeedDemoDataJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        if (! Post::query()->where('is_seeded', true)->exists()) {
            Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--no-interaction' => true]);
        }

        if (Setting::get('locations_mode', 'single') === 'multiple') {
            $this->seedMultipleLocations();
        } else {
            Artisan::call('db:seed', ['--class' => 'LocationSeeder', '--no-interaction' => true]);
        }

        Setting::set('seeding_status', 'complete');
    }

    public function failed(\Throwable $exception): void
    {
        Setting::set('seeding_status', 'failed');
    }

    private function seedMultipleLocations(): void
    {
        $locations = [
            [
                'name' => 'GetRows Austin',
                'address' => '100 Congress Avenue',
                'city' => 'Austin',
                'state' => 'TX',
                'zip' => '78701',
                'phone' => '(512) 555-0101',
                'is_seeded' => true,
            ],
            [
                'name' => 'GetRows San Antonio',
                'address' => '200 E Market Street',
                'city' => 'San Antonio',
                'state' => 'TX',
                'zip' => '78205',
                'phone' => '(210) 555-0202',
                'is_seeded' => true,
            ],
            [
                'name' => 'GetRows Memphis',
                'address' => '350 Beale Street',
                'city' => 'Memphis',
                'state' => 'TN',
                'zip' => '38103',
                'phone' => '(901) 555-0303',
                'is_seeded' => true,
            ],
            [
                'name' => 'GetRows Tulsa',
                'address' => '75 W 5th Street',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip' => '74103',
                'phone' => '(918) 555-0404',
                'is_seeded' => true,
            ],
            [
                'name' => 'GetRows Shreveport',
                'address' => '500 Texas Street',
                'city' => 'Shreveport',
                'state' => 'LA',
                'zip' => '71101',
                'phone' => '(318) 555-0505',
                'is_seeded' => true,
            ],
        ];

        foreach ($locations as $location) {
            Location::firstOrCreate(['name' => $location['name']], $location);
        }
    }
}
