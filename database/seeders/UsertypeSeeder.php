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
        // Usertype::factory()->count(4)->create();
        Usertype::create([
            'name' => 'admin'
        ]);
        Usertype::create([
            'name' => 'mobileSeller'
        ]);
        Usertype::create([
            'name' => 'electronicDeviceSeller'
        ]);
        Usertype::create([
            'name' => 'accessoriesSeller'
        ]);
    }
}
