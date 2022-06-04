<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BorrowerFactory extends Factory
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
            'initialBorrow' => $this->faker->numberBetween($min = 1000, $max = 9000),
            'balance' => $this->faker->numberBetween($min = 1000, $max = 9000),
            'telephone' => $this->faker->randomNumber($nbDigits = NULL, $strict = false),
            'description' => $this->faker->sentence($nbWords = 6, $variableNbWords = true),
            'address' => $this->faker->state,
            'balance' => $this->faker->numberBetween($min = 1000, $max = 9000),

        ];
    }
}
