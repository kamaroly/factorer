<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanRateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_rates', function (Blueprint $table) {

            $table->increments('id')->index();
            $table->integer('start_month')->unique()->index();
            $table->integer('end_month')->unique()->index();
            $table->decimal('rate',10,2);
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
        Schema::drop('loan_rates');
    }
}
