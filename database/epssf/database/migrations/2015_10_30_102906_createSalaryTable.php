<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::create('salaries', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->dateTime('amount');
            $table->dateTime('tax');
            $table->integer('deduction');
            $table->string('bonus');
            $table->string('month');
            $table->string('year');
            $table->timestamps();
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
        Schema::drop('salaries');
    }
}
