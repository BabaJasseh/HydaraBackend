<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CashExpenditureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'initialExpense' => $this->faker->numberBetween($min = 1000, $max = 9000) ,
            'categoryName' => $this->faker->name,
            'address' => $this->faker->state,
            'description' => $this->faker->sentence($nbWords = 6, $variableNbWords = true),


        ];
    }
}
