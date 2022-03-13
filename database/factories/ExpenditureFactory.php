<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenditureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'amount' => $this->faker->numberBetween($min = 1000, $max = 9000) ,
            'name' => $this->faker->name,
            'description' => $this->faker->sentence($nbWords = 6, $variableNbWords = true),

        ];
    }
}
