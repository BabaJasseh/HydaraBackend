<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creditors', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id');
            $table->string('sellername');
            $table->string('customername');
            $table->integer('totalSalePrice');
            $table->integer('amountpaid');
            $table->integer('balance');
            $table->integer('status');  /// 1 for completed 0 for incomplete
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
        Schema::dropIfExists('creditors');
    }
}