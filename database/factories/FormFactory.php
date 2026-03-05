<?php

namespace Database\Factories;

use App\Enums\FormType;
use App\Models\Form;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Form>
 */
class FormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true).' Form',
            'type' => FormType::Contact->value,
            'notification_email' => null,
            'save_submissions' => true,
            'fields' => Form::defaultFields(),
            'is_seeded' => false,
        ];
    }

    public function withEmail(): static
    {
        return $this->state(['notification_email' => fake()->safeEmail()]);
    }

    public function withPhoneEnabled(): static
    {
        return $this->state(function (): array {
            $fields = Form::defaultFields();
            $fields['phone']['enabled'] = true;
            $fields['phone']['required'] = false;

            return ['fields' => $fields];
        });
    }

    public function jobApplication(): static
    {
        return $this->state([
            'type' => FormType::JobApplication->value,
            'fields' => FormType::JobApplication->defaultFields(),
        ]);
    }

    public function photoContest(): static
    {
        return $this->state([
            'type' => FormType::PhotoContest->value,
            'fields' => FormType::PhotoContest->defaultFields(),
        ]);
    }
}
