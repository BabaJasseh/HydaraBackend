<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
                'name' => $this->faker->randomElement($array = array ('Mobiles','Accessories','Electronic Devices')),
                'date' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
        ];
    }
}
