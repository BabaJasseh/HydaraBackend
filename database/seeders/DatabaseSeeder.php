<?php

namespace Database\Seeders;

use App\Models\User;
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
         $this->call(BrandSeeder::class);
         $this->call(ProductSeeder::class);
         $this->call(SalarySeeder::class);
         $this->call(ShopExpenditureSeeder::class);
         $this->call(CashExpenditureSeeder::class);
         $this->call(StaffSeeder::class);
         $this->call(DepositorSeeder::class);
         $this->call(SaleSeeder::class);
         $this->call(BorrowerSeeder::class);
         $this->call(UserTypeSeeder::class);
         $this->call(UserSeeder::class);
         $this->call(TransactionSeeder::class);
         $this->call(StockSeeder::class);
         $this->call(PaymentSeeder::class);
         $this->call(BorrowerTransactionSeeder::class);
    }
}
