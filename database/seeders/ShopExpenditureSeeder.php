<?php

namespace Database\Seeders;

use App\Models\Shopexpenditure;
use Illuminate\Database\Seeder;

class ShopExpenditureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Shopexpenditure::factory()->count(20)->create();

    }
}
