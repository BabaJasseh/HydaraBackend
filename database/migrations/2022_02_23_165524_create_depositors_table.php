<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('depositors', function (Blueprint $table) {
            $table->id();  
            $table->string('firstname');
            $table->string('lastname');
            $table->string('description');
            $table->string('telephone');
            $table->string('address');
            $table->integer('initialDeposit');
            $table->integer('balance');
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
        Schema::dropIfExists('depositors');
    }
}
