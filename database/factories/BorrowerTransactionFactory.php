<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BorrowerTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'borrower_id' => $this->faker->randomElement($array = array ('2','1','3')),
            'action' => $this->faker->randomElement($array = array ('repay','borrow')),
            'description' => $this->faker->sentence($nbWords = 6, $variableNbWords = true),
            'amount' => $this->faker->numberBetween($min = 1000, $max = 9000),

        ];
    }
}
