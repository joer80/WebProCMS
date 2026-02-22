<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'GetRows Austin',
                'address' => '100 Congress Avenue',
                'city' => 'Austin',
                'state' => 'TX',
                'zip' => '78701',
                'phone' => '(512) 555-0101',
                'photo' => null,
            ],
            [
                'name' => 'GetRows San Antonio',
                'address' => '200 E Market Street',
                'city' => 'San Antonio',
                'state' => 'TX',
                'zip' => '78205',
                'phone' => '(210) 555-0202',
                'photo' => null,
            ],
            [
                'name' => 'GetRows Memphis',
                'address' => '350 Beale Street',
                'city' => 'Memphis',
                'state' => 'TN',
                'zip' => '38103',
                'phone' => '(901) 555-0303',
                'photo' => null,
            ],
            [
                'name' => 'GetRows Tulsa',
                'address' => '75 W 5th Street',
                'city' => 'Tulsa',
                'state' => 'OK',
                'zip' => '74103',
                'phone' => '(918) 555-0404',
                'photo' => null,
            ],
            [
                'name' => 'GetRows Shreveport',
                'address' => '500 Texas Street',
                'city' => 'Shreveport',
                'state' => 'LA',
                'zip' => '71101',
                'phone' => '(318) 555-0505',
                'photo' => null,
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
