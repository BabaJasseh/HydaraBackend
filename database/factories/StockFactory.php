<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category_id' => $this->faker->randomElement($array = array ('2','1','3')),
            'name' => $this->faker->randomElement($array = array ('thinkpad','iphone10','flatscreen', 'smart computer')),
            // 'description' => $this->faker->sentence($nbWords = 6, $variableNbWords = true),
            'costprice' => $this->faker->numberBetween($min = 1000, $max = 9000) ,
            'quantity' => $this->faker->randomElement($array = array ('5','10','6', '4')),
            'status' => $this->faker->randomElement($array = array ('1')),
        ];
    }
}
