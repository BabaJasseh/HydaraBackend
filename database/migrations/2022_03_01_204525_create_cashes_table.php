<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashes', function (Blueprint $table) {
            $table->id();
            $table->integer('totalCreditAmount')->default(0);
            $table->integer('totalDepositAmount')->default(0);
            $table->integer('totalBorrowedAmount')->default(0);
            $table->integer('totalExpense')->default(0);
            $table->integer('cashAthand')->default(0);
            $table->integer('currentBalance')->default(0);
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
        Schema::dropIfExists('cashes');
    }
}
