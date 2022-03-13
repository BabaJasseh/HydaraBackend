<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'amountpaid' => $this->faker->numberBetween($min = 1000, $max = 9000) ,
            'sellingprice' => $this->faker->numberBetween($min = 1000, $max = 9000) ,
            'seller' => $this->faker->name,
            'customername' => $this->faker->name,
            'profit' => $this->faker->numberBetween($min = 900, $max = 3000),
            'date' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
            'category_id' => $this->faker->randomElement($array = array ('2','1','3', '4')),
            'date' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
        ];
    }
}
