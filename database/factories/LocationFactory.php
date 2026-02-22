<?php

namespace Database\Factories;

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

        return [
            'name' => 'GetRows '.$city,
            'address' => fake()->streetAddress(),
            'city' => $city,
            'state' => fake()->stateAbbr(),
            'zip' => fake()->postcode(),
            'phone' => fake()->phoneNumber(),
            'photo' => 'https://picsum.photos/seed/getrows-'.$seed.'/600/400',
        ];
    }
}
