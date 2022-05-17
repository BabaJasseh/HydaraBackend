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
            $table->string('customername');
            $table->string('seller');
            $table->integer('amountpaid');
            $table->integer('balance');
            $table->integer('totalSalePrice');
            $table->integer('profit')->default(0);
            $table->string('date');
            $table->string('status');
            $table->timestamps();
            $table->foreignId('category_id')->constrained('categories')->onUpdate('cascade');

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
