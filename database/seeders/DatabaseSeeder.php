<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //  \App\Models\User::factory(10)->create();
         $this->call(CategorySeeder::class);
         $this->call(ProductSeeder::class);
         $this->call(BrandSeeder::class);
         $this->call(SalarySeeder::class);
         $this->call(ExpenditureSeeder::class);
         $this->call(StaffSeeder::class);
         $this->call(DepositorSeeder::class);
         $this->call(SaleSeeder::class);
         $this->call(BorrowerSeeder::class);
    }
}
