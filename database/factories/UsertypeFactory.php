<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UsertypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement($array = array('admin', 'seller', 'cashier')),
        ];
    }
}
