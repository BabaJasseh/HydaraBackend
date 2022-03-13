<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'firstname' => $this->faker->firstNameMale,
            'lastname' => $this->faker->lastName,
            'salary' => $this->faker->numberBetween($min = 1000, $max = 9000) ,
            'address' => $this->faker->state,
            'telephone' => $this->faker->randomNumber($nbDigits = NULL, $strict = false) ,
        ];
    }
}
