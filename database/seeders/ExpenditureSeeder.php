<?php

namespace Database\Seeders;

use App\Models\Expenditure;
use Illuminate\Database\Seeder;

class ExpenditureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Expenditure::factory()->count(20)->create();

    }
}
