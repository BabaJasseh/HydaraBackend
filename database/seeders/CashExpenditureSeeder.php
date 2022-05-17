<?php

namespace Database\Seeders;

use App\Models\Cashexpenditure;
use Illuminate\Database\Seeder;

class CashExpenditureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cashexpenditure::factory()->count(20)->create();

    }
}
