<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id');
            $table->string('customername');
            $table->string('seller');
            $table->integer('amountpaid');
            $table->integer('totalSalePrice');
            $table->string('profit');
            $table->string('sellingprice');
            $table->string('date');
            $table->string('status');
            // $table->integer('productsbought');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
