<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'name' => 'Mobiles',
            'date' => '2022-06-08'
        ]);
        Category::create([
            'name' => 'Accessories',
            'date' => '2022-06-08'
        ]);
        Category::create([
            'name' => 'Electronic Devices',
            'date' => '2022-06-08'
        ]);
    }
}
