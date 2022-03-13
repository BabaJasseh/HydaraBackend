<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SalaryFactory extends Factory
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
            'staffname' => $this->faker->name,
            'month' => $this->faker->monthName($max = 'now'),
            'date' => $this->faker->date($format = 'Y-m-d', $max = 'now'),

        ];
    }
}
