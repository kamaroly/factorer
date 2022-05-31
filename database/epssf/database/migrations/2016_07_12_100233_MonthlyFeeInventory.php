<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MonthlyFeeInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_fee_inventory', function(Blueprint $table){
            $table->increments('id');
            $table->string('amount');
            $table->string('type')->default('increase');
            $table->string('adhersion_id');
            $table->timeStamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('monthly_fee_inventory');
    }
}
