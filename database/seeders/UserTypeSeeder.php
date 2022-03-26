<?php

namespace Database\Seeders;

use App\Models\Usertype as ModelsUsertype;
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
        ModelsUsertype::factory()->count(4)->create();
    }
}
