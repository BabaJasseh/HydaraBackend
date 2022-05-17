<?php

namespace Database\Seeders;

use App\Models\Borrowertransaction;
use Illuminate\Database\Seeder;

class BorrowerTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Borrowertransaction::factory()->count(10)->create();
    }
}
