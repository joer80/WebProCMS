<?php

namespace Database\Factories;

use App\Support\States;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = fake()->city();
        $seed = strtolower(str_replace(' ', '-', $city));

        $state = fake()->stateAbbr();

        return [
            'name' => 'WebProCMS '.$city,
            'address' => fake()->streetAddress(),
            'city' => $city,
            'state' => $state,
            'state_full' => States::fullName($state),
            'zip' => fake()->postcode(),
            'phone' => fake()->phoneNumber(),
            'photo' => 'https://picsum.photos/seed/webprocms-'.$seed.'/600/400',
        ];
    }
}
