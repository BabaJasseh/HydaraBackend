<?php

namespace Database\Seeders;

use App\Models\Depositor;
use Illuminate\Database\Seeder;

class DepositorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Depositor::factory()->count(20)->create();

    }
}
