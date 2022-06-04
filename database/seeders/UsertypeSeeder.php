<?php

namespace Database\Seeders;

use App\Models\Usertype;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Usertype::factory()->count(4)->create();
    }
}
